<?php
namespace App\Command;
use App\Entity\{DemoRecord, HelpCenterArticle, HelpCenterCategory, Settings};
use App\Service\HelpCenterCatalog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
#[AsCommand(name:'app:help-center:seed',description:'Populate reviewed Deree demo help without reseeding accounts or activity.')]
final class HelpCenterSeedCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,private HelpCenterCatalog $catalog,#[Autowire('%env(bool:APP_DEMO)%')] private bool $demo) { parent::__construct(); }
    protected function configure(): void { $this->addOption('force',null,InputOption::VALUE_NONE,'Reapply catalogue content to demo-owned help records.'); }
    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        if (!$this->demo) { $output->writeln('Help catalogue skipped: APP_DEMO is disabled.'); return self::SUCCESS; }
        $data=$this->catalog->load(); $version=$this->catalog->version();
        $state=$this->em->getRepository(Settings::class)->findOneBy(['key'=>'help_center_catalog_version']);
        if ($state?->getValue()===$version && !$input->getOption('force')) {
            $output->writeln('Help catalogue already installed; administrator edits preserved.'); return self::SUCCESS;
        }
        $registrations=[]; $counts=['categories'=>0,'articles'=>0,'preserved'=>0];
        $resolve=function(string $class,array $row) use (&$registrations,&$counts): ?object {
            $record=$this->em->find(DemoRecord::class,$row['key']);
            if ($record && $record->entityClass!==$class) throw new \RuntimeException('Help record ownership does not match its entity type.');
            $entity=$record ? $this->em->find($class,$record->entityId) : null;
            if (!$entity) {
                $entity=$this->em->getRepository($class)->findOneBy(['slug'=>$row['slug']]);
                if ($entity && !$record) { $counts['preserved']++; return null; }
            }
            if (!$entity) { $entity=new $class(); $entity->setSlug($row['slug']); $this->em->persist($entity); }
            $registrations[]=[$entity,$class,$row,$record];
            return $entity;
        };
        $this->em->wrapInTransaction(function() use ($data,$version,&$state,&$registrations,&$counts,$resolve): void {
            $categories=[];
            foreach ($data['categories'] as $row) {
                $category=$resolve(HelpCenterCategory::class,$row);
                if (!$category) { $categories[$row['key']]=$this->em->getRepository(HelpCenterCategory::class)->findOneBy(['slug'=>$row['slug']]); continue; }
                $category->setName($row['name']); $category->setIcon($row['icon']); $category->setHidden(false);
                $categories[$row['key']]=$category; $counts['categories']++;
            }
            foreach ($data['articles'] as $row) {
                $article=$resolve(HelpCenterArticle::class,$row); if (!$article) continue;
                $article->setTitle($row['title']); $article->setContent($row['content']); $article->setTags(implode(',',$row['tags']));
                $article->setCategory($categories[$row['category']]); $article->setHidden(false); $article->setFeatured($row['featured']);
                $counts['articles']++;
            }
            $this->em->flush();
            foreach ($registrations as [$entity,$class,$row,$record]) {
                $provenance=['catalog'=>'deree-help-center','verified_at'=>$data['verified_at'],'source_keys'=>$row['sources']??[]];
                if ($record) { $record->entityId=(string)$entity->getId(); $record->provenance=$provenance; }
                else $this->em->persist(new DemoRecord($row['key'],$class,(string)$entity->getId(),$provenance));
            }
            if (!$state) { $state=new Settings(); $state->setKey('help_center_catalog_version'); $this->em->persist($state); }
            $state->setValue($version); $this->em->flush();
        });
        $output->writeln(sprintf('Help populated: %d categories, %d articles; %d administrator records preserved.',$counts['categories'],$counts['articles'],$counts['preserved']));
        return self::SUCCESS;
    }
}
