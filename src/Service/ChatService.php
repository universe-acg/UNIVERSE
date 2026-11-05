<?php
namespace App\Service;
use App\Entity\{UniverseChatEntity,UniverseChatUser,UniverseMessage,User};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\{AccessDeniedHttpException,NotFoundHttpException};
final class ChatService
{
    public function __construct(private EntityManagerInterface $em, #[Autowire('%kernel.secret%')] private string $secret) {}
    public function rooms(User $user): array {
        return $this->em->createQueryBuilder()->select('DISTINCT c')->from(UniverseChatEntity::class,'c')->join('c.participants','u')->where('u = :user')->setParameter('user',$user)->orderBy('c.id','DESC')->getQuery()->getResult();
    }
    public function room(int $id,User $user): UniverseChatEntity {
        $room=$this->em->find(UniverseChatEntity::class,$id);if(!$room)throw new NotFoundHttpException();
        if(!$room->getParticipants()->contains($user))throw new AccessDeniedHttpException();return $room;
    }
    public function messages(UniverseChatEntity $room,int $after=0): array {
        $messages=$this->em->getRepository(UniverseMessage::class)->createQueryBuilder('m')->join('m.sender','u')->addSelect('u')->where('m.universeChat = :room')->andWhere('m.id > :after')->setParameter('room',$room)->setParameter('after',$after)->orderBy('m.id',$after>0?'ASC':'DESC')->setMaxResults(200)->getQuery()->getResult();
        if($after===0)$messages=array_reverse($messages);return $messages;
    }
    public function send(UniverseChatEntity $room,User $user,string $text): UniverseMessage {
        $this->room($room->getId(),$user);$text=trim($text);
        if($text===''||mb_strlen($text)>4000)throw new \InvalidArgumentException('Enter a message of 1 to 4000 characters.');
        $message=new UniverseMessage();$message->setUniverseChat($room);$message->setSender($user);
        foreach($room->getParticipants() as $recipient)if($recipient->getId()!==$user->getId()){$message->setReceiver($recipient);break;}
        $message->setContent($text);$message->setSentAt(new \DateTime());$this->em->persist($message);$this->em->flush();return $message;
    }
    public function serialize(UniverseMessage $message,User $user): array {
        return ['id'=>$message->getId(),'issender'=>$message->getSender()->getId()!==$user->getId(),'msg'=>['contact'=>['name'=>$message->getSender()->getCrossRoleName(),'datetimeSent'=>$message->getSentAt()->format(DATE_ATOM)],'txt'=>$message->getContent()]];
    }
    public function token(User $user,int $room): string {
        $payload=$user->getId().':'.$room.':'.(time()+3600);return base64_encode($payload).'.'.hash_hmac('sha256',$payload,$this->secret);
    }
    public function resolveToken(string $token): array {
        $parts=explode('.',$token,2);$payload=base64_decode($parts[0],true);
        if($payload===false||count($parts)!==2||!hash_equals(hash_hmac('sha256',$payload,$this->secret),$parts[1]))throw new AccessDeniedHttpException();
        $values=explode(':',$payload);if(count($values)!==3||(int)$values[2]<time())throw new AccessDeniedHttpException();
        $user=$this->em->find(User::class,(int)$values[0]);if(!$user||!$user->isEnabled()||$user->getDeletedAt())throw new AccessDeniedHttpException();
        return [$user,$this->room((int)$values[1],$user)];
    }
    public function add(UniverseChatEntity $room,User $user): void {
        if(!$room->getParticipants()->contains($user))$room->getParticipants()->add($user);
        if(!$this->em->find(UniverseChatUser::class,['universeChat'=>$room,'user'=>$user])){$member=new UniverseChatUser();$member->setUniverseChat($room);$member->setUser($user);$this->em->persist($member);}
        $this->em->flush();
    }
}
