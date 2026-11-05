<?php
namespace App\Command;

use App\Entity\{DemoRecord, Page};
use App\Service\PublicPageContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:pages:sync', description: 'Install useful demo information pages while preserving administrator edits.')]
final class SyncPublicPagesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private PublicPageContent $content,
        #[Autowire('%env(bool:APP_DEMO)%')] private bool $demo) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->demo) { $output->writeln('Skipped: demo mode is disabled.'); return self::SUCCESS; }
        $updated = 0;
        foreach ($this->content->load() as $slug => $definition) {
            $record = $this->em->find(DemoRecord::class, 'page-'.$slug);
            $page = $record ? $this->em->find(Page::class, $record->entityId) : null;
            if (!$page || $record->entityClass !== Page::class) { $output->writeln('Preserved unowned page: '.$slug); continue; }
            if (!PublicPageContent::mayUpdate($page->getContent() ?? '', $record->provenance['publicContentHash'] ?? null)) { $output->writeln('Preserved administrator content: '.$slug); continue; }
            if (isset($record->provenance['publicContentTitle']) && $page->getTitle() !== $record->provenance['publicContentTitle']) { $output->writeln('Preserved administrator title: '.$slug); continue; }
            $record->provenance['publicContentTitle'] = $definition['title'];
            if ($page->getContent() === $definition['content'] && $page->getTitle() === $definition['title']) continue;
            // Keep established URLs stable: title-based slug listeners must not rename these pages.
            $this->em->getConnection()->update('universe_page', ['title' => $definition['title'], 'content' => $definition['content']], ['id' => $page->getId()]);
            $record->provenance['publicContentHash'] = hash('sha256', $definition['content']); ++$updated;
        }
        $this->em->flush(); $output->writeln(sprintf('%d information pages updated.', $updated));
        return self::SUCCESS;
    }
}
