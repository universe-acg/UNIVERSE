<?php
namespace App\Service;
use App\Entity\{UniverseChatEntity,UniverseChatUser,UniverseMessage,User};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException,NotFoundHttpException};
final class ChatService
{
    public function __construct(private EntityManagerInterface $em, #[Autowire('%kernel.secret%')] private string $secret) {}
    public function rooms(User $user): array
    {
        return $this->em->createQueryBuilder()->select('c')->addSelect('(SELECT MAX(m.id) FROM App\\Entity\\UniverseMessage m WHERE m.universeChat=c) AS HIDDEN latest')
            ->from(UniverseChatEntity::class,'c')->join('c.participants','u')->where('u=:user')->setParameter('user',$user)
            ->orderBy('latest','DESC')->addOrderBy('c.id','DESC')->getQuery()->getResult();
    }
    public function room(int $id,User $user): UniverseChatEntity
    {
        $room=$this->em->find(UniverseChatEntity::class,$id);
        if (!$room) throw new NotFoundHttpException('Conversation not found.');
        if (!$room->getParticipants()->contains($user)) throw new AccessDeniedHttpException();
        return $room;
    }
    public function direct(User $user,User $recipient): UniverseChatEntity
    {
        if ($user->getId()===$recipient->getId() || !$recipient->isEnabled() || $recipient->getDeletedAt()) throw new \InvalidArgumentException('Choose another active user.');
        $ids=[$user->getId(),$recipient->getId()]; sort($ids,SORT_NUMERIC); $key=implode(':',$ids);
        // MySQL 8 atomic upserts prevent two simultaneous starts from creating duplicate rooms.
        $id=$this->em->getConnection()->transactional(function($db) use ($key,$ids): int {
            $db->executeStatement('INSERT INTO universe_chat (title,direct_key) VALUES (?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)',['Conversation',$key]);
            $id=(int)$db->lastInsertId();
            foreach ($ids as $userId) {
                $db->executeStatement('INSERT IGNORE INTO chat_user (universe_chat_entity_id,user_id) VALUES (?,?)',[$id,$userId]);
                $db->executeStatement('INSERT IGNORE INTO universe_chat_user (chat_id,user_id,last_read_message_id,muted) VALUES (?,?,0,0)',[$id,$userId]);
            }
            return $id;
        });
        return $this->room($id,$user);
    }
    public function messages(UniverseChatEntity $room,int $after=0,int $before=0): array
    {
        $qb=$this->em->getRepository(UniverseMessage::class)->createQueryBuilder('m')->join('m.sender','u')->addSelect('u')
            ->where('m.universeChat=:room')->setParameter('room',$room);
        if ($after>0) $qb->andWhere('m.id>:after')->setParameter('after',$after);
        if ($before>0) $qb->andWhere('m.id<:before')->setParameter('before',$before);
        $messages=$qb->orderBy('m.id',$after>0?'ASC':'DESC')->setMaxResults(200)->getQuery()->getResult();
        return $after>0 ? $messages : array_reverse($messages);
    }
    public static function validateMessage(string $text,?string $clientId=null): string
    {
        $text=trim(str_replace("\0",'',$text));
        if ($text==='' || mb_strlen($text)>4000) throw new \InvalidArgumentException('Enter a message of 1 to 4000 characters.');
        if ($clientId!==null && !preg_match('/^[a-zA-Z0-9_-]{16,64}$/',$clientId)) throw new \InvalidArgumentException('Invalid message identifier.');
        return $text;
    }
    public function send(UniverseChatEntity $room,User $user,string $text,?string $clientId=null): UniverseMessage
    {
        $this->room($room->getId(),$user); $text=self::validateMessage($text,$clientId); $receiver=null;
        foreach ($room->getParticipants() as $recipient) if ($recipient->getId()!==$user->getId()) { $receiver=$recipient; break; }
        if (!$receiver) throw new \InvalidArgumentException('This conversation has no other participant.');
        $db=$this->em->getConnection();
        $db->executeStatement('INSERT INTO universe_message (universe_chat_id,sender_id,receiver_id,content,sent_at,client_id) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)',
            [$room->getId(),$user->getId(),$receiver->getId(),$text,(new \DateTime())->format('Y-m-d H:i:s'),$clientId]);
        return $this->em->find(UniverseMessage::class,(int)$db->lastInsertId());
    }
    public function serialize(UniverseMessage $message,User $user): array
    {
        return ['id'=>$message->getId(),'clientId'=>$message->getClientId(),'issender'=>$message->getSender()->getId()!==$user->getId(),
            'msg'=>['contact'=>['name'=>$message->getSender()->getCrossRoleName(),'datetimeSent'=>$message->getSentAt()->format(DATE_ATOM)],'txt'=>$message->getContent()]];
    }
    public function membership(UniverseChatEntity $room,User $user): UniverseChatUser
    {
        $this->room($room->getId(),$user);
        $member=$this->em->find(UniverseChatUser::class,['universeChat'=>$room,'user'=>$user]);
        if (!$member) { $member=new UniverseChatUser(); $member->setUniverseChat($room); $member->setUser($user); $this->em->persist($member); $this->em->flush(); }
        return $member;
    }
    public function unread(UniverseChatEntity $room,User $user): int
    {
        $member=$this->membership($room,$user);
        return (int)$this->em->getRepository(UniverseMessage::class)->createQueryBuilder('m')->select('COUNT(m.id)')
            ->where('m.universeChat=:room AND m.sender!=:user AND m.id>:after')->setParameter('room',$room)->setParameter('user',$user)->setParameter('after',$member->getLastReadMessageId())->getQuery()->getSingleScalarResult();
    }
    public function markRead(UniverseChatEntity $room,User $user,int $id): void
    {
        $latest=$this->em->getRepository(UniverseMessage::class)->findOneBy(['universeChat'=>$room],['id'=>'DESC']);
        $this->membership($room,$user)->setLastReadMessageId(max(0,min($id,$latest?->getId()??0))); $this->em->flush();
    }
    public function token(User $user,int $room): string
    {
        $payload=$user->getId().':'.$room.':'.(time()+3600); return base64_encode($payload).'.'.hash_hmac('sha256',$payload,$this->secret);
    }
    public function resolveToken(string $token): array
    {
        $parts=explode('.',$token,2); $payload=base64_decode($parts[0],true);
        if ($payload===false || count($parts)!==2 || !hash_equals(hash_hmac('sha256',$payload,$this->secret),$parts[1])) throw new AccessDeniedHttpException();
        $values=explode(':',$payload); if (count($values)!==3 || (int)$values[2]<time()) throw new AccessDeniedHttpException();
        $user=$this->em->find(User::class,(int)$values[0]); if (!$user || !$user->isEnabled() || $user->getDeletedAt()) throw new AccessDeniedHttpException();
        return [$user,$this->room((int)$values[1],$user)];
    }
    public function add(UniverseChatEntity $room,User $user): void
    {
        if (!$user->isEnabled() || $user->getDeletedAt()) throw new \InvalidArgumentException('User is unavailable.');
        if (!$room->getParticipants()->contains($user)) $room->getParticipants()->add($user);
        $room->setDirectKey(null);
        if (!$this->em->find(UniverseChatUser::class,['universeChat'=>$room,'user'=>$user])) { $member=new UniverseChatUser(); $member->setUniverseChat($room); $member->setUser($user); $this->em->persist($member); }
        $this->em->flush();
    }
}
