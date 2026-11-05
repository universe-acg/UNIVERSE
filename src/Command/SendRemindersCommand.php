<?php
namespace App\Command;
use App\Entity\{Notification,OrderElement};
use App\Service\{DemoMailer,MailMessage};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
#[AsCommand(name: 'app:send-reminders', description: 'Capture one reminder per attendee and event session today.')]
final class SendRemindersCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private DemoMailer $mailer, private UrlGeneratorInterface $router) { parent::__construct(); }
    protected function configure(): void { $this->addOption('date', null, InputOption::VALUE_REQUIRED, 'Reference date, YYYY-MM-DD')->addOption('dry-run', null, InputOption::VALUE_NONE); }
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $date=$input->getOption('date') ?: date('Y-m-d');
        $start=\DateTimeImmutable::createFromFormat('!Y-m-d',$date,new \DateTimeZone('Europe/Athens'));
        if(!$start || $start->format('Y-m-d')!==$date) { $output->writeln('<error>Invalid date.</error>'); return Command::INVALID; }
        $lock=fopen(sys_get_temp_dir().'/universe-reminders-'.hash('sha256',__DIR__).'.lock','c');
        if(!$lock || !flock($lock,LOCK_EX|LOCK_NB)) { $output->writeln('Reminder job already running.');return Command::SUCCESS; }
        try {
            $elements=$this->em->createQueryBuilder()->select('oe,o,u,t,d,e')->from(OrderElement::class,'oe')->join('oe.order','o')->join('o.user','u')->join('oe.eventticket','t')->join('t.eventdate','d')->join('d.event','e')->where('o.status = 1')->andWhere('d.startdate >= :start AND d.startdate < :end')->setParameter('start',$start)->setParameter('end',$start->modify('+1 day'))->getQuery()->getResult();
            $seen=[];$count=0;
            foreach($elements as $element) {
                $user=$element->getOrder()->getUser();$session=$element->getEventticket()->getEventdate();$event=$session->getEvent();
                $key=$user->getId().':'.$session->getId();if(isset($seen[$key]))continue;$seen[$key]=true;
                $title='Event reminder';$description='Session '.$session->getId().' on '.$date.': '.mb_substr($event->getName(),0,180);
                if($this->em->getRepository(Notification::class)->findOneBy(['userid'=>$user->getId(),'name'=>$title,'description'=>$description]))continue;
                if(!$input->getOption('dry-run')) {
                    $url=$this->router->generate('event',['slug'=>$event->getSlug()],UrlGeneratorInterface::ABSOLUTE_URL);
                    $body='Kind reminder: '.$event->getName().' starts '.$session->getStartdate()->format('d M Y H:i').'. '.$url;
                    $this->mailer->send((new MailMessage($title))->setFrom('noreply@universe.example.test')->setTo($user->getEmail())->setBody($body,'text/plain'));
                    $notification=new Notification();$notification->setUserid($user->getId());$notification->setName($title);$notification->setDescription($description);$notification->setRead(false);$this->em->persist($notification);$this->em->flush();
                }
                $count++;
            }
            $output->writeln(sprintf('%d reminders %s.',$count,$input->getOption('dry-run')?'eligible':'captured'));return Command::SUCCESS;
        } finally { flock($lock,LOCK_UN);fclose($lock); }
    }
}
