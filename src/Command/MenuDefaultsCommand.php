<?php
namespace App\Command;
use App\Entity\{Menu,MenuElement,DemoRecord,Settings};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
#[AsCommand(name:'app:menus:sync',description:'Repair default navigation without reseeding users or demo activity.')]
final class MenuDefaultsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,#[Autowire('%kernel.project_dir%')] private string $projectDir){parent::__construct();}
    protected function configure(): void {$this->addOption('force',null,InputOption::VALUE_NONE,'Reapply defaults to demo-owned menu entries.');}
    protected function execute(InputInterface $input,OutputInterface $output): int {
        $json=file_get_contents($this->projectDir.'/data/ui-menus.json');$version=hash('sha256',$json);
        $state=$this->em->getRepository(Settings::class)->findOneBy(['key'=>'navigation_defaults_version']);
        if($state?->getValue()===$version&&!$input->getOption('force')){$output->writeln('Navigation defaults already installed; administrator changes preserved.');return self::SUCCESS;}
        $new=[];$count=0;
        $this->em->wrapInTransaction(function()use($json,$version,&$state,&$new,&$count){
            foreach(json_decode($json,true,512,JSON_THROW_ON_ERROR) as $row){
                $menu=$this->em->getRepository(Menu::class)->findOneBy(['slug'=>$row['slug']]);
                if(!$menu){$menu=new Menu();$menu->setName($row['name']);$menu->setSlug($row['slug']);$this->em->persist($menu);$new[]=[$menu,'menu-'.$row['slug'],Menu::class];}
                if(!$menu->getHeader())$menu->setHeader($row['header']??$row['name']);
                foreach($row['elements'] as $item){
                    $key='menu-'.$row['slug'].'-'.md5($item['label']);$record=$this->em->find(DemoRecord::class,$key);$element=$record?$this->em->find(MenuElement::class,$record->entityId):null;
                    if(!$element){
                        foreach($menu->getMenuElements() as $existing)if(($existing->getLink()?:$existing->getCustomLink())===($item['link']?:$item['customLink'])){$element=$existing;break;}
                        // Existing custom entries remain owned by the administrator.
                        if($element&&!$record)continue;
                    }
                    if(!$element){$element=new MenuElement();$element->setSlug($item['slug']);$menu->addMenuElement($element);$this->em->persist($element);$new[]=[$element,$key,MenuElement::class];}
                    if($record && $element->getId())$record->entityId=(string)$element->getId();
                    $element->setLabel($item['label']);$element->setIcon($item['icon']);$element->setLink($item['link']);$element->setCustomLink($item['customLink']);$element->setPosition($item['position']);$element->setAudience($item['audience']);$element->setPlacement($item['placement']);$count++;
                }
            }
            $this->em->flush();
            foreach($new as [$element,$key,$class]){
                $record=$this->em->find(DemoRecord::class,$key);
                if($record){$record->entityClass=$class;$record->entityId=(string)$element->getId();}
                else $this->em->persist(new DemoRecord($key,$class,(string)$element->getId(),['source'=>'navigation defaults']));
            }
            if(!$state){$state=new Settings();$state->setKey('navigation_defaults_version');$this->em->persist($state);}$state->setValue($version);$this->em->flush();
        });
        $output->writeln(sprintf('Navigation repaired: %d default entries. Other records were not reseeded.',$count));return self::SUCCESS;
    }
}
