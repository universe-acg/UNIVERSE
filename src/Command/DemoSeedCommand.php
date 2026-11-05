<?php
namespace App\Command;

use App\Entity as E;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:demo:seed', description: 'Create or update the isolated Deree demonstration dataset without purging data.')]
final class DemoSeedCommand extends Command
{
    private array $records=[];
    private array $objects=[];
    private array $pool=[];
    private \DateTime $reference;
    private int $seed=20261105;
    private string $passwordHash;
    private string $demoPassword;
    public function __construct(private EntityManagerInterface $em, #[Autowire('%kernel.project_dir%')] private string $projectDir, #[Autowire('%env(bool:APP_DEMO)%')] private bool $demo) { parent::__construct(); }
    protected function configure(): void {
        $this->addOption('seed', null, InputOption::VALUE_REQUIRED, 'Deterministic seed', '20261105')
            ->addOption('reference-date', null, InputOption::VALUE_REQUIRED, 'YYYY-MM-DD in Europe/Athens', date('Y-m-d'));
    }
    private function make(string $class, string $key, array $values=[], array $provenance=[]): object {
        $key=substr($key,0,180);
        if (isset($this->objects[$key])) return $this->objects[$key][0];
        $existing=$this->records[$key]??null;
        $identifier=$existing && str_starts_with($existing->entityId,'{') ? json_decode($existing->entityId,true,512,JSON_THROW_ON_ERROR) : $existing?->entityId;
        $object=$existing ? $this->em->find($class,$identifier) : null;
        if (!$object) $object=new $class();
        $meta=$this->em->getClassMetadata($class);
        foreach(array_keys($values) as $field) if(!$meta->hasField($field) && !$meta->hasAssociation($field)) throw new \RuntimeException('Unknown fixture field '.$class.'#'.$field);
        foreach ($meta->fieldMappings as $field=>$mapping) {
            if ($meta->isIdentifier($field) && $meta->usesIdGenerator()) continue;
            if (array_key_exists($field,$values)) { $value=$values[$field]; }
            elseif ($existing) continue;
            elseif ($field==='updatedAt') { $value=clone $this->reference; }
            elseif ($mapping->nullable) { $value=null; }
            else {
                $value=match($mapping->type) {
                    'integer','smallint','bigint'=>0, 'float','decimal'=>0, 'boolean'=>false,
                    'json','simple_array'=>[], 'datetime','date','time'=>clone $this->reference,
                    'datetime_immutable','date_immutable'=>\DateTimeImmutable::createFromMutable($this->reference),
                    default=>str_contains(strtolower($field),'email') ? $key.'@example.test' : $key,
                };
                if (is_string($value) && $mapping->length) $value=mb_substr($value,0,$mapping->length);
            }
            $meta->setFieldValue($object,$field,$value);
        }
        foreach ($meta->associationMappings as $field=>$mapping) {
            if (array_key_exists($field,$values)) $meta->setFieldValue($object,$field,$values[$field]);
            elseif (!$existing && $mapping->isToMany()) $meta->setFieldValue($object,$field,new ArrayCollection());
        }
        $this->em->persist($object);
        $this->objects[$key]=[$object,$class,$provenance];
        $this->pool[$class][]=$object;
        return $object;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int {
        if (!$this->demo) { $output->writeln('<error>APP_DEMO=1 is required. No records changed.</error>'); return self::FAILURE; }
        $date=(string)$input->getOption('reference-date');
        $this->reference=\DateTime::createFromFormat('!Y-m-d',$date,new \DateTimeZone('Europe/Athens'));
        if (!$this->reference || $this->reference->format('Y-m-d')!==$date) { $output->writeln('<error>Invalid reference date.</error>'); return self::FAILURE; }
        $this->seed=(int)$input->getOption('seed');
        $credentialPath=$this->projectDir.'/var/demo-credentials.json';
        $credentials=is_file($credentialPath) ? json_decode(file_get_contents($credentialPath),true,512,JSON_THROW_ON_ERROR) : ['password'=>bin2hex(random_bytes(14))];
        $this->demoPassword=$credentials['password'];
        $this->passwordHash=password_hash($this->demoPassword,PASSWORD_BCRYPT,['cost'=>10]);
        foreach ($this->em->getRepository(E\DemoRecord::class)->findAll() as $record) $this->records[$record->recordKey]=$record;
        $this->em->getConnection()->beginTransaction();
        try {
            $this->seedDataset($output);
            // Required relationships are resolved only after all parent records exist.
            foreach ($this->objects as [$object,$class]) {
                $meta=$this->em->getClassMetadata($class);
                foreach ($meta->associationMappings as $field=>$mapping) {
                    if (!$mapping->isToOne() || !$mapping->isOwningSide() || $meta->getFieldValue($object,$field)) continue;
                    $required=false;
                    foreach ($mapping->joinColumns as $column) if ($column->nullable===false) $required=true;
                    if ($required) {
                        $target=$this->pool[$mapping->targetEntity][0]??null;
                        if (!$target) throw new \RuntimeException('Missing fixture dependency '.$class.'#'.$field);
                        $meta->setFieldValue($object,$field,$target);
                    }
                }
            }
            $this->em->flush();
            $studentPool=array_values(array_filter($this->pool[E\User::class]??[],fn($user)=>$user->hasRole('ROLE_ATTENDEE')));
            foreach($this->objects as $key=>[$object,$class]) {
                if($object instanceof E\Notification){$index=(int)substr($key,strlen('notification-'));$object->setUserid($studentPool[$index%count($studentPool)]->getId());}
                if($object instanceof E\VolunteerEvent){$index=(int)substr($key,strlen('volunteer-'));$object->setUniverseEventId($this->pool[E\Event::class][$index]->getId());}
            }
            foreach ($this->objects as $key=>[$object,$class,$provenance]) {
                $ids=$this->em->getClassMetadata($class)->getIdentifierValues($object);
                foreach($ids as &$identifierValue)if(is_object($identifierValue))$identifierValue=$this->em->getClassMetadata($identifierValue::class)->getIdentifierValues($identifierValue)['id'];unset($identifierValue);
                $id=count($ids)===1 && isset($ids['id']) ? (string)$ids['id'] : json_encode($ids,JSON_THROW_ON_ERROR);
                if (!isset($this->records[$key])) $this->em->persist(new E\DemoRecord($key,$class,$id,$provenance));
            }
            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Throwable $error) { $this->em->getConnection()->rollBack(); throw $error; }
        $credentials['accounts']=['administrator'=>'demo-admin','student'=>'student-001','organizer'=>'organizer-001'];
        if (!is_dir(dirname($credentialPath))) mkdir(dirname($credentialPath),0700,true);
        file_put_contents($credentialPath,json_encode($credentials,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));chmod($credentialPath,0600);
        $counts=[];foreach ($this->objects as [,$class]) $counts[$class]=($counts[$class]??0)+1;
        file_put_contents($this->projectDir.'/var/demo-seed-report.json',json_encode(['seed'=>$this->seed,'reference_date'=>$date,'counts'=>$counts],JSON_PRETTY_PRINT));
        $output->writeln('<info>Seed complete: '.count($this->objects).' records. Credentials stored privately in var/demo-credentials.json.</info>');
        return self::SUCCESS;
    }
    private function seedDataset(OutputInterface $output): void {
        $admin=$this->user('demo-admin','Demo','Administrator',['ROLE_ADMINISTRATOR']);
        $this->user('demo-admin-2','Campus','Administrator',['ROLE_ADMINISTRATOR']);
        $first=['Alex','Maria','Nikos','Eleni','Sofia','Andreas','Iris','Dimitris','Anna','Jason','Maya','Omar','Chloe','Sam','Leila','Daniel','Nina','Aris','Zoe','Theo'];
        $last=['Georgiou','Papadaki','Nikolaou','Anderson','Williams','Ioannou','Petrou','Karalis','Demetriou','Kostas','Rossi','Martin','Haddad','Chen','Novak','Ali','Wilson','Patel','Khan','Silva'];
        $students=[];
        for($i=0;$i<500;$i++) $students[]=$this->user(sprintf('student-%03d',$i+1),$first[($i+$this->seed)%20],$last[(int)floor($i/20)%20],['ROLE_ATTENDEE']);
        $output->writeln('Prepared 502 fictional student/admin accounts.');
        $directory=json_decode(file_get_contents($this->projectDir.'/data/deree/directory.json'),true,512,JSON_THROW_ON_ERROR);
        $orgs=[];$venues=[];$facilities=[];$categories=[];
        foreach(['Academic','Arts & Culture','Community','Career','Sports','Technology','Sustainability'] as $i=>$name) $categories[]=$this->make(E\Category::class,'category-'.$i,['name'=>$name,'slug'=>'category-'.$i,'hidden'=>false]);
        foreach($directory as $row) {
            $key=substr(hash('sha256',$row['kind'].'|'.$row['name']),0,16);
            $provenance=['source_url'=>$row['source_url'],'verification'=>$row['verification'],'retrieved_at'=>$row['retrieved_at']];
            if (in_array($row['kind'],['club','society','organization'])) {
                $i=count($orgs);$user=$this->user(sprintf('organizer-%03d',$i+1),'Demo','Coordinator',['ROLE_ORGANIZER']);
                $org=$this->make(E\Organizer::class,'org-'.$key,['name'=>$row['name'],'slug'=>'deree-'.$key,'category'=>['club'=>'Club','society'=>'Society','organization'=>'Organization'][$row['kind']], 'description'=>$row['summary']??('Explore '.$row['name'].' in the Deree campus community. Organization information is sourced from the official directory; all member activity here is fictional.'),'email'=>null,'website'=>$row['source_url'],'user'=>$user,'categories'=>new ArrayCollection([$categories[$i%count($categories)]]),'isathletics'=>false],$provenance);
                $user->setOrganizer($org);$orgs[]=$org;
                for($j=0;$j<12;$j++) {
                    $student=$students[($i*7+$j)%500];
                    $this->make(E\ManagementClubsSocieties::class,'membership-'.$key.'-'.$j,['org'=>$org,'user'=>$student,'initiatedBy'=>$student,'status'=>'1']);
                    if($j<4) $this->make(E\UniverseGbMember::class,'gb-'.$key.'-'.$j,['org'=>$org,'attendee'=>$student,'title'=>['Demo President','Demo Vice President','Demo Secretary','Demo Treasurer'][$j],'desc'=>'Fictional demonstration officer.']);
                    if (!$student->getFollowing()->contains($org)) $student->getFollowing()->add($org);
                }
            } elseif($row['kind']==='venue') $venues[]=$this->make(E\Venue::class,'venue-'.$key,['title'=>$row['name'],'slug'=>'venue-'.$key,'description'=>'Campus venue listed in the official ACG venue directory.'], $provenance);
            elseif($row['kind']==='facility') $facilities[]=$this->make(E\FacilityEntity::class,'facility-'.$key,['name'=>$row['name'],'slug'=>'facility-'.$key,'minPlayers'=>1,'maxPlayers'=>12,'duration'=>60,'hidden'=>false,'published'=>true],$provenance);
        }
        if(!$orgs || !$venues || !$facilities) throw new \RuntimeException('Research dataset must include organizations, venues and facilities.');
        $dates=[];$tickets=[];$events=[];
        foreach($orgs as $i=>$org) {
            foreach(['Welcome gathering','Practical workshop','Community collaboration'] as $j=>$activity) {
                $when=(clone $this->reference)->modify('+'.(7+(int)floor($i/8)+$j*28).' days')->setTime(14+($i%4),30);
                if((int)$when->format('N')>5) $when->modify('next monday');
                $key='demo-event-'.$i.'-'.$j;
                $event=$this->make(E\Event::class,$key,['name'=>$org->getName().': '.$activity,'slug'=>$key,'description'=>'<p>Fictional demo activity. Meet fellow students, exchange ideas and participate in a welcoming session hosted in this demonstration by '.htmlspecialchars($org->getName()).'.</p>','organizer'=>$org,'venue'=>$venues[$i%count($venues)],'category'=>$categories[$i%count($categories)],'published'=>true,'open'=>true]);
                [$date,$ticket]=$this->eventSession($event,$key,$when);$dates[]=$date;$tickets[]=$ticket;$events[]=$event;
                foreach(array_slice($students,($i*3)%450,4) as $student) if (!$event->getAddedtofavoritesby()->contains($student)) $event->getAddedtofavoritesby()->add($student);
            }
        }
        $archiveUser=$this->user('archive-caretaker','Demo','Archive caretaker',['ROLE_ORGANIZER']);
        $archiveOrg=$this->make(E\Organizer::class,'archive-organizer',['name'=>'ACG College Events archive','slug'=>'acg-events-archive','category'=>'organization','description'=>'Archive caretaker in this demo; event-specific organizers are identified by the linked official sources.','user'=>$archiveUser,'website'=>'https://www.acg.edu/news-events/department-of-college-events-office-of-the-president/past-events/']);
        $archiveUser->setOrganizer($archiveOrg);
        $history=json_decode(file_get_contents($this->projectDir.'/data/deree/event_facts.json'),true,512,JSON_THROW_ON_ERROR);
        $activeArchiveKeys=[]; $archiveSessions=[];
        foreach($history as $i=>$row) {
            $key='archive-'.substr(hash('sha256',$row['source_id']??$row['title'].'|'.($row['start']??'undated')),0,20);
            $activeArchiveKeys[$key]=true;
            $event=$this->make(E\Event::class,$key,['name'=>mb_substr($row['title'],0,255),'slug'=>$key,'description'=>'<p>Documented ACG event. Original details: <a href="'.htmlspecialchars($row['source_url']).'">official event source</a>. Demonstration registrations are fictional.</p>','organizer'=>$archiveOrg,'venue'=>null,'category'=>$categories[0],'published'=>!empty($row['start']),'open'=>true],$row);
            if(!empty($row['start'])) { $sessionKey=isset($archiveSessions[$key])?$key.'-session-'.substr(hash('sha256',$row['start']),0,8):$key; $archiveSessions[$key]=true; $this->eventSession($event,$sessionKey,new \DateTime($row['start'],new \DateTimeZone('Europe/Athens')),empty($row['end'])?null:new \DateTime($row['end'],new \DateTimeZone('Europe/Athens')),true); }
        }
        // Retire only duplicated archive fixtures owned by previous demo imports.
        foreach($this->records as $key=>$record) {
            if($record->entityClass!==E\Event::class||!str_starts_with($key,'archive-')||isset($activeArchiveKeys[$key]))continue;
            $obsolete=$this->em->find(E\Event::class,$record->entityId);
            if($obsolete)$this->em->remove($obsolete);
            foreach([$key,$key.'-date',$key.'-admission'] as $ownedKey)if(isset($this->records[$ownedKey])){$this->em->remove($this->records[$ownedKey]);unset($this->records[$ownedKey]);}
        }
        for($i=0;$i<2000;$i++) {
            $ticket=$tickets[$i%count($tickets)];$student=$students[$i%500];$key='registration-'.$i;
            $order=$this->make(E\Order::class,$key,['user'=>$student,'reference'=>'DEMO-'.sprintf('%06d',$i),'status'=>$i%17===0?0:1,'overbook'=>false]);
            $element=$this->make(E\OrderElement::class,$key.'-element',['order'=>$order,'eventticket'=>$ticket,'quantity'=>1]);
            $this->make(E\OrderTicket::class,$key.'-ticket',['orderelement'=>$element,'reference'=>hash('sha256','demo-ticket-'.$this->seed.'-'.$i)]);
            if($i<60) $this->make(E\TicketReservation::class,$key.'-hold',['user'=>$student,'eventticket'=>$ticket,'orderelement'=>$element,'quantity'=>1]);
        }
        $output->writeln('Prepared researched organizations, documented events and 2,000 fictional registrations.');
        foreach($facilities as $i=>$facility) {
            $schedule=$this->make(E\MonthlySchedule::class,'schedule-'.$i,['facility'=>$facility,'startDate'=>(clone $this->reference)->modify('first day of this month'),'endDate'=>(clone $this->reference)->modify('+5 months')->modify('last day of this month')]);
            foreach(['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day) $this->make(E\OperatingHours::class,'hours-'.$i.'-'.$day,['monthlySchedule'=>$schedule,'dayOfWeek'=>$day,'startTime'=>new \DateTime('09:00'),'endTime'=>new \DateTime('22:00')]);
            for($j=0;$j<25;$j++) {
                $when=(clone $this->reference)->modify('+'.($j+1).' days')->setTime(10+$i%5,0);
                $this->make(E\AthleticBooking::class,'booking-'.$i.'-'.$j,['facility'=>$facility,'user'=>$students[($i*25+$j)%500],'bookingDate'=>$when,'duration'=>60,'amount'=>1,'status'=>true]);
            }
        }
        $listingCategory=$this->make(E\ListingCategory::class,'listing-category',['name'=>'Campus opportunities','slug'=>'campus-opportunities']);
        for($i=0;$i<60;$i++) {
            $listing=$this->make(E\Listing::class,'listing-'.$i,['name'=>['Student project assistant','Campus volunteer opportunity','Creative collaboration','Peer mentoring opportunity'][$i%4].' · '.$orgs[$i%count($orgs)]->getName(),'slug'=>'demo-opportunity-'.$i,'organizer'=>$orgs[$i%count($orgs)],'category'=>$listingCategory,'description'=>'Fictional campus opportunity for demonstration applications.','published'=>true]);
            $position=$this->make(E\ListingDatePosition::class,'listing-position-'.$i,['listing'=>$listing,'startdate'=>(clone $this->reference)->modify('+14 days'),'enddate'=>(clone $this->reference)->modify('+90 days'),'candidatesamount'=>8]);
            for($j=0;$j<3;$j++) $this->make(E\ListingSubmission::class,'submission-'.$i.'-'.$j,['listing'=>$listing,'position'=>$position,'user'=>$students[($i*3+$j)%500],'content'=>'I would like to contribute my skills and work with fellow students on this fictional campus project.']);
        }
        $blogCategory=$this->make(E\BlogPostCategory::class,'blog-category',['name'=>'Campus community','slug'=>'campus-community']);
        for($i=0;$i<100;$i++) $this->make(E\BlogPost::class,'news-'.$i,['name'=>'Campus connections '.$i,'slug'=>'campus-connections-'.$i,'content'=>'<p>Demo campus update: students share ideas, practice teamwork and prepare their next community activity. This story is fictional.</p>','category'=>$blogCategory,'user'=>$orgs[$i%count($orgs)]->getUser(),'hidden'=>false]);
        for($i=0;$i<60;$i++) {
            $reel=$this->make(E\Reel::class,'reel-'.$i,['title'=>'Demo campus moment '.$i,'content'=>'Fictional snapshot from campus life.','type'=>'image','user'=>$students[$i],'slug'=>'demo-reel-'.$i]);
            $this->make(E\ReelLike::class,'like-'.$i,['reel'=>$reel,'user'=>$students[($i+1)%500]]);
        }
        $help=$this->make(E\HelpCenterCategory::class,'help-category',['name'=>'Using the demo','slug'=>'using-the-demo']);
        foreach(['Getting started','Joining an organization','Booking an activity','Privacy and demo data'] as $i=>$title) $this->make(E\HelpCenterArticle::class,'help-'.$i,['title'=>$title,'slug'=>'demo-help-'.$i,'category'=>$help,'content'=>'This demonstration uses fictional student accounts and activity. Explore the directory, follow organizations, and register for demo activities. External delivery is captured locally.']);
        foreach(['about-us'=>'About UNIVERSE','privacy-policy'=>'Demo privacy information','cookie-policy'=>'Session cookies','legal-notice'=>'Demonstration notice','community-guidelines'=>'Community guidelines','contact'=>'Contact information'] as $slug=>$title) $this->make(E\Page::class,'page-'.$slug,['title'=>$title,'slug'=>$slug,'content'=>'<p>UNIVERSE is a campus-community demonstration. Organization facts are researched from official ACG sources. Student accounts and interactions are fictional. Essential session cookies support sign-in.</p>']);
        foreach(['website_name'=>'Universe','website_slug'=>'universe-deree-demo','app_theme'=>'orange','website_description_en'=>'Discover the Deree campus community.','website_keywords_en'=>'Deree, clubs, campus, events','google_recaptcha_enabled'=>'no','website_root_url'=>'http://127.0.0.1:8098','no_reply_email'=>'noreply@universe.example.test'] as $key=>$value) $this->make(E\Settings::class,'setting-'.$key,['key'=>$key,'value'=>$value]);
        $this->make(E\AppLayoutSettings::class,'layout',['logoName'=>'64901bc67a4fa291758359.png','logoSize'=>15871,'logoMimeType'=>'image/png','logoOriginalName'=>'Universe.png','logoDimensions'=>[500,152],'faviconName'=>'64901bc67ac2c306344789.png','faviconSize'=>15000,'faviconMimeType'=>'image/png','faviconOriginalName'=>'Universe icon.png','faviconDimensions'=>[500,500],'ogImageName'=>'64901bc67a4fa291758359.png']);$this->make(E\HomepageHeroSettings::class,'homepage-hero');
        $menuKeys=[];
        foreach(json_decode(file_get_contents($this->projectDir.'/data/ui-menus.json'),true,512,JSON_THROW_ON_ERROR) as $originalMenu) {
            $menuSlug=$originalMenu['slug'];$menu=$this->make(E\Menu::class,'menu-'.$menuSlug,['name'=>$originalMenu['name'],'slug'=>$menuSlug,'header'=>$originalMenu['header']??null]);
            foreach($originalMenu['elements'] as $element){$key='menu-'.$menuSlug.'-'.md5($element['label']);$menuKeys[$key]=true;$this->make(E\MenuElement::class,$key,$element+['menu'=>$menu]);}
        }
        // Remove only navigation rows owned by an earlier demo seed, never user-created items.
        foreach($this->records as $key=>$record){
            if($record->entityClass===E\MenuElement::class && !isset($menuKeys[$key])){
                $obsolete=$this->em->find(E\MenuElement::class,$record->entityId);if($obsolete)$this->em->remove($obsolete);$this->em->remove($record);
            }
        }
        foreach(['app_layout'=>'container','terms_of_service_page_slug'=>'legal-notice','privacy_policy_page_slug'=>'privacy-policy','website_currency_symbol'=>'€','website_currency_position'=>'left','website_date_format'=>'d M Y','website_timezone'=>'Europe/Athens'] as $key=>$value) $this->make(E\Settings::class,'setting-'.$key,['key'=>$key,'value'=>$value]);
        foreach(json_decode(file_get_contents($this->projectDir.'/data/ui-settings.json'),true,512,JSON_THROW_ON_ERROR) as $key=>$value){$setting=$this->objects['setting-'.$key][0]??$this->make(E\Settings::class,'setting-'.$key,['key'=>$key,'value'=>$value]);$setting->setValue($value);}
        $amenity=$this->make(E\Amenity::class,'amenity-accessible',['name'=>'Accessible campus space','slug'=>'accessible-campus-space','icon'=>'fas fa-universal-access','hidden'=>false]);
        foreach($venues as $venue) { if(!$venue->getAmenities()->contains($amenity))$venue->getAmenities()->add($amenity); }
        foreach($events as $i=>$event) {
            $thread=$this->make(E\Thread::class,'thread-'.$i,['id'=>$event->getSlug(),'permalink'=>'/event/'.$event->getSlug(),'isCommentable'=>true,'numComments'=>2]);
            foreach([0,1] as $j) {
                $comment=$this->make(E\Comment::class,'comment-'.$i.'-'.$j,['thread'=>$thread,'author'=>$students[($i+$j)%500],'body'=>['Looking forward to meeting everyone at this demo activity.','Will there be an introduction for students joining for the first time?'][$j],'state'=>0,'score'=>1]);
                $this->make(E\Vote::class,'vote-'.$i.'-'.$j,['comment'=>$comment,'voter'=>$students[($i+$j+5)%500],'value'=>1]);
            }
            if($i<30)$this->make(E\VolunteerEvent::class,'volunteer-'.$i,['universeEventId'=>$i+1,'user'=>$students[$i],'message'=>'I can help welcome participants at this fictional demo activity.']);
        }
        for($i=0;$i<30;$i++) {
            $chat=$this->make(E\UniverseChatEntity::class,'chat-'.$i,['title'=>'Demo project group '.($i+1),'participants'=>new ArrayCollection([$students[$i],$students[$i+1]])]);
            foreach([0,1] as $participant)$this->make(E\UniverseChatUser::class,'chat-member-'.$i.'-'.$participant,['universeChat'=>$chat,'user'=>$students[$i+$participant]]);
            for($j=0;$j<4;$j++) $this->make(E\UniverseMessage::class,'message-'.$i.'-'.$j,['universeChat'=>$chat,'sender'=>$students[$i+($j%2)],'receiver'=>$students[$i+(1-$j%2)],'content'=>['Welcome to our demo project group.','Let’s meet before the campus workshop.','I will prepare the discussion notes.','Thanks, see you at the venue.'][$j],'sentAt'=>(clone $this->reference)->modify('-'.(30-$j).' minutes')]);
        }
        for($i=0;$i<100;$i++) $this->make(E\Notification::class,'notification-'.$i,['name'=>'Demo campus update','userid'=>0,'description'=>'A fictional activity from one of your organizations is open for registration.','read'=>$i%3===0]);
        for($i=0;$i<4;$i++)$this->make(E\Live::class,'live-'.$i,['title'=>'Demo live-session showcase '.($i+1),'slug'=>'demo-live-'.$i,'hidden'=>false,'description'=>'Provider streaming is not configured. This record demonstrates live-session management.','link'=>'','status'=>false]);
        $this->makeDemoDocuments($students,$events,$venues,$facilities);
    }
    private function makeDemoDocuments(array $students,array $events,array $venues,array $facilities): void {
        $private=$this->projectDir.'/var/private/demo';if(!is_dir($private))mkdir($private,0700,true);
        $pdf=$private.'/demo-document.pdf';
        if(!is_file($pdf)){$document=new \Dompdf\Dompdf();$document->loadHtml('<h1>UNIVERSE demo document</h1><p>Fictional campus project summary. No real student information.</p>');$document->render();file_put_contents($pdf,$document->output());chmod($pdf,0600);}
        foreach(['files','cvfiles','doc_file1','doc_file2','applications'] as $folder){$dir=$this->projectDir.'/var/private/'.$folder;if(!is_dir($dir))mkdir($dir,0700,true);copy($pdf,$dir.'/demo-document.pdf');chmod($dir.'/demo-document.pdf',0600);}
        // These public copies are fictional event flyers, not personal documents.
        foreach(['files','listings'] as $folder){$dir=$this->projectDir.'/public/uploads/'.$folder;if(!is_dir($dir))mkdir($dir,0755,true);copy($pdf,$dir.'/demo-document.pdf');}
        $metadata=['fileName'=>'demo-document.pdf','fileSize'=>filesize($pdf),'fileMimeType'=>'application/pdf','fileOriginalName'=>'Demo campus document.pdf'];
        foreach(array_slice($students,0,20) as $i=>$student) {
            $cv=$this->make(E\CV::class,'cv-'.$i,$metadata+['user'=>$student]);$student->setCv($cv);
            $doc=$this->make(E\DocDocumentDTO::class,'document-'.$i,$metadata+['user'=>$student,'fileName1'=>'demo-document.pdf','fileSize1'=>filesize($pdf),'fileMimeType1'=>'application/pdf','fileOriginalName1'=>'Demo project summary.pdf']);$student->setDoc($doc);
            $this->make(E\DocDocument1::class,'document-one-'.$i,$metadata+['user'=>$student]);
            $this->make(E\DocDocument2::class,'document-two-'.$i,$metadata+['user'=>$student]);
            $this->make(E\File::class,'file-'.$i,$metadata+['user'=>$student,'title'=>'Demo campus project']);
        }
        foreach(array_slice($events,0,12) as $i=>$event)$this->make(E\EventFile::class,'event-file-'.$i,$metadata+['event'=>$event,'position'=>0]);
        foreach(array_slice($this->pool[E\Listing::class],0,12) as $i=>$listing)$this->make(E\ListingFile::class,'listing-file-'.$i,$metadata+['listing'=>$listing,'position'=>0]);
        $this->makeDemoImages($events,$venues,$facilities);
    }
    private function makeDemoImages(array $events,array $venues,array $facilities): void {
        $image=imagecreatetruecolor(1200,675);$navy=imagecolorallocate($image,21,59,112);$light=imagecolorallocate($image,241,244,249);$gold=imagecolorallocate($image,230,168,61);
        imagefill($image,0,0,$navy);imagefilledrectangle($image,80,540,1120,548,$gold);imagestring($image,5,80,90,'UNIVERSE | DEREE CAMPUS DEMO',$light);imagestring($image,4,80,125,'Illustrative demonstration artwork',$light);
        for($i=0;$i<7;$i++)imagefilledrectangle($image,100+$i*145,260,170+$i*145,510,$light);
        $private=$this->projectDir.'/var/private/demo';$file=$private.'/campus-demo.jpg';imagejpeg($image,$file,88);imagedestroy($image);
        foreach(['events','venues','facilities','listings','reels','lives','blog','categories','layout','organizers','organizers/covers','users/avatars','homepage/hero'] as $folder){$dir=$this->projectDir.'/public/uploads/'.$folder;if(!is_dir($dir))mkdir($dir,0755,true);copy($file,$dir.'/campus-demo.jpg');}
        foreach($this->objects as [$object,$class]) {
            $meta=$this->em->getClassMetadata($class);
            foreach(['imageName','mainImage','coverName','avatarName','customBackgroundName'] as $field)if($meta->hasField($field))$meta->setFieldValue($object,$field,'campus-demo.jpg');
            foreach(['imageSize','coverSize','avatarSize','customBackgroundSize'] as $field)if($meta->hasField($field))$meta->setFieldValue($object,$field,filesize($file));
        }
        $imageData=['imageName'=>'campus-demo.jpg','imageSize'=>filesize($file),'imageMimeType'=>'image/jpeg','imageOriginalName'=>'Illustrative demo campus.jpg','imageDimensions'=>[1200,675],'position'=>0];
        foreach(array_slice($events,0,12) as $i=>$event)$this->make(E\EventImage::class,'event-image-'.$i,$imageData+['event'=>$event]);
        foreach(array_slice($venues,0,12) as $i=>$venue)$this->make(E\VenueImage::class,'venue-image-'.$i,$imageData+['venue'=>$venue]);
        foreach($facilities as $i=>$facility)$this->make(E\FacilityEntityImage::class,'facility-image-'.$i,$imageData+['facility'=>$facility]);
        foreach(array_slice($this->pool[E\Listing::class],0,12) as $i=>$listing)$this->make(E\ListingImage::class,'listing-image-'.$i,$imageData+['listing'=>$listing]);
    }
    private function user(string $username,string $first,string $last,array $roles): E\User {
        $existingUser=$this->em->getRepository(E\User::class)->findOneBy(['username'=>$username]);
        $password=$existingUser && password_verify($this->demoPassword,$existingUser->getPassword())?$existingUser->getPassword():$this->passwordHash;
        return $this->make(E\User::class,'user-'.$username,['username'=>$username,'usernameCanonical'=>$username,'email'=>$username.'@example.test','emailCanonical'=>$username.'@example.test','password'=>$password,'roles'=>$roles,'enabled'=>true,'firstname'=>$first,'lastname'=>$last,'slug'=>$username,'studentid'=>'DEMO-'.substr(hash('sha256',$username),0,10),'points'=>0]);
    }
    private function eventSession(E\Event $event,string $key,\DateTime $when,?\DateTime $end=null,bool $documented=false): array {
        $date=$this->make(E\EventDate::class,$key.'-date',['event'=>$event,'reference'=>$key.'-date','title'=>$event->getName(),'description'=>'Demo activity session','startdate'=>$when,'enddate'=>$documented?$end:(clone $when)->modify('+90 minutes'),'active'=>true]);
        $ticket=$this->make(E\EventTicket::class,$key.'-admission',['eventdate'=>$date,'reference'=>$key.'-admission','name'=>'General admission','quantity'=>100,'ticketsperattendee'=>1,'salesstartdate'=>(clone $when)->modify('-60 days'),'salesenddate'=>(clone $when)->modify('-1 hour'),'active'=>true,'position'=>0]);
        return [$date,$ticket];
    }
}
