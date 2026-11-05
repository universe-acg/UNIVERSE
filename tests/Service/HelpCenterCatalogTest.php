<?php
namespace App\Tests\Service;
use App\Service\HelpCenterCatalog;
use App\Entity\HelpCenterArticle;
use App\Twig\SortFaqsExtension;
use PHPUnit\Framework\TestCase;
final class HelpCenterCatalogTest extends TestCase
{
    public function testFullCatalogueHasUsefulAnswersAndKnownSources(): void
    {
        $data=(new HelpCenterCatalog(dirname(__DIR__,2)))->load();
        self::assertCount(9,$data['categories']); self::assertCount(72,$data['articles']);
        self::assertCount(12,array_filter($data['articles'],static fn($a)=>$a['featured']));
        foreach ($data['categories'] as $category) self::assertCount(8,array_filter($data['articles'],static fn($a)=>$a['category']===$category['key']));
        foreach ($data['articles'] as $article) {
            self::assertGreaterThan(255,strlen(strip_tags($article['content'])),$article['slug']);
            self::assertStringNotContainsString('<script',$article['content']);
            foreach ($article['sources'] as $key) self::assertStringStartsWith('https://www.acg.edu/',$data['sources'][$key]['url']);
        }
        self::assertCount(72,array_unique(array_column($data['articles'],'content')));
    }
    public function testFaqTitlesUseNaturalSorting(): void
    {
        $articles=[];
        foreach (['10. Tickets','2. Account','What is a club?','How do I join?'] as $title) { $article=new HelpCenterArticle(); $article->setTitle($title); $articles[]=$article; }
        $sorted=(new SortFaqsExtension())->sortFaqs($articles);
        self::assertSame(['2. Account','10. Tickets','How do I join?','What is a club?'],array_map(static fn($a)=>$a->getTitle(),$sorted));
    }
}
