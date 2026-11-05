<?php
namespace App\Service;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
/** Shared, reviewed definitions for full demo seeding and scoped help updates. */
final class HelpCenterCatalog
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir) {}
    public function load(): array
    {
        $data=json_decode(file_get_contents($this->projectDir.'/data/deree/help-center.json'),true,512,JSON_THROW_ON_ERROR);
        $keys=[]; $slugs=[]; $categories=[];
        foreach ($data['categories'] as $row) {
            if (isset($keys[$row['key']]) || isset($slugs[$row['slug']])) throw new \RuntimeException('Duplicate help category.');
            $keys[$row['key']]=true; $slugs[$row['slug']]=true; $categories[$row['key']]=true;
        }
        $slugs=[];
        foreach ($data['articles'] as $row) {
            if (isset($keys[$row['key']]) || isset($slugs[$row['slug']]) || !isset($categories[$row['category']])) throw new \RuntimeException('Invalid help article identity or category.');
            if (mb_strlen($row['title'])>255 || mb_strlen(implode(',',$row['tags']))>255 || trim(strip_tags($row['content']))==='') throw new \RuntimeException('Invalid help article content.');
            foreach ($row['sources'] as $source) if (!isset($data['sources'][$source])) throw new \RuntimeException('Unknown help source.');
            $keys[$row['key']]=true; $slugs[$row['slug']]=true;
        }
        return $data;
    }
    public function version(): string
    {
        return hash_file('sha256',$this->projectDir.'/data/deree/help-center.json');
    }
}
