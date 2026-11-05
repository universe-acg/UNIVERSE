<?php
namespace App\Command;
use App\Entity\{DemoRecord,Settings,Reel,Live,User,UniverseChatEntity,UniverseChatUser,UniverseMessage};
use App\Service\{CommunityDemoData,DemoImageInstaller};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
#[AsCommand(name:'app:community:seed',description:'Enrich demo media, rankings and conversations without recreating accounts.')]
final class CommunitySeedCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,private CommunityDemoData $data,private DemoImageInstaller $imageInstaller,#[Autowire('%env(bool:APP_DEMO)%')] private bool $demo) { parent::__construct(); }
    protected function configure(): void { $this->addOption('force',null,InputOption::VALUE_NONE,'Reapply demo-owned community examples.'); }
    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        if (!$this->demo) { $output->writeln('Community seed skipped: APP_DEMO is disabled.'); return self::SUCCESS; }
        $this->data->installAssets(); $this->imageInstaller->installAllAssets(); require dirname(__DIR__,2).'/scripts/install_badges.php'; $data=$this->data->load(); $version=$this->data->version(); $state=$this->em->getRepository(Settings::class)->findOneBy(['key'=>'community_demo_version']);
        if ($state && $this->data->isCompatibleLegacyVersion($state->getValue()) && !$input->getOption('force')) { $state->setValue($version); $this->em->flush(); }
        if ($state?->getValue()===$version && !$input->getOption('force')) { $output->writeln('Community examples already installed; user changes preserved.'); return self::SUCCESS; }
        $pending=[];
        $owned=function(string $class,string $key) use (&$pending): object {
            $record=$this->em->find(DemoRecord::class,$key); $id=$record && str_starts_with($record->entityId,'{') ? json_decode($record->entityId,true,512,JSON_THROW_ON_ERROR) : $record?->entityId; $object=$record?$this->em->find($class,$id):null;
            if (!$object) { $object=new $class(); if (!$object instanceof UniverseChatUser) $this->em->persist($object); }
            $pending[]=[$object,$class,$key,$record]; return $object;
        };
        $user=function(string $name): ?User {
            $record=$this->em->find(DemoRecord::class,'user-'.$name); return $record?$this->em->find(User::class,$record->entityId):null;
        };
        $this->em->wrapInTransaction(function() use ($data,$version,&$state,&$pending,$owned,$user): void {
            foreach ($this->em->getRepository(DemoRecord::class)->findBy(['entityClass'=>User::class]) as $record) {
                $account=$this->em->find(User::class,$record->entityId); if ($account && !$account->getPoints()) $account->setPoints(CommunityDemoData::points($account->getUsername()));
            }
            foreach ($data['reels'] as $i=>$row) {
                $account=$user(sprintf('student-%03d',$i+1)); if (!$account) continue;
                $reel=$owned(Reel::class,$row['key']);
                foreach (['slug','title','content','type','mainImage','videoFileName','tags'] as $field) $reel->{'set'.ucfirst($field)}($row[$field]);
                $reel->setLinkvideo(''); $reel->setUser($account); $reel->setHidden(false);
            }
            foreach ($data['lives'] as $row) {
                $owner=$user($row['owner']); if (!$owner) continue; $live=$owned(Live::class,$row['key']);
                foreach (['slug','title','description','mainImage','link','status'] as $field) $live->{'set'.ucfirst($field)}($row[$field]);
                $live->setOwner($owner); $live->setHidden(false);
            }
            foreach ($data['rooms'] as $row) {
                $members=array_map($user,$row['users']); if (in_array(null,$members,true)) continue;
                $room=$owned(UniverseChatEntity::class,$row['key']); $room->setTitle($row['title']);
                foreach ($members as $i=>$member) {
                    if (!$room->getParticipants()->contains($member)) $room->getParticipants()->add($member);
                    $link=$owned(UniverseChatUser::class,$row['key'].'-member-'.$i); $link->setUniverseChat($room); $link->setUser($member); $this->em->persist($link);
                }
                foreach ($row['messages'] as $i=>$text) {
                    $key=$row['key'].'-message-'.$i; if ($this->em->find(DemoRecord::class,$key)) continue;
                    $message=$owned(UniverseMessage::class,$key); $message->setUniverseChat($room); $message->setSender($members[$i%count($members)]); $message->setReceiver($members[($i+1)%count($members)]); $message->setContent($text); $message->setSentAt((new \DateTime())->modify('-'.(30-$i*3).' minutes'));
                }
            }
            $this->em->flush();
            foreach ($pending as [$object,$class,$key,$record]) {
                if ($object instanceof UniverseChatUser) $id=json_encode(['universeChat'=>$object->getUniverseChat()->getId(),'user'=>$object->getUser()->getId()],JSON_THROW_ON_ERROR);
                else $id=(string)$object->getId();
                if ($record) $record->entityId=$id; else $this->em->persist(new DemoRecord($key,$class,$id,['source'=>'fictional community demonstration']));
            }
            if (!$state) { $state=new Settings(); $state->setKey('community_demo_version'); $this->em->persist($state); } $state->setValue($version); $this->em->flush();
        });
        $output->writeln('Community examples populated: 60 reels, four playable previews, five group conversations and demo participation points.'); return self::SUCCESS;
    }
}
