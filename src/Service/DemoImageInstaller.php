<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class DemoImageInstaller
{
    private ?array $mapping = null;

    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir) {}

    public function load(): array
    {
        if ($this->mapping === null) {
            $this->mapping = json_decode(file_get_contents($this->projectDir.'/data/deree/demo-images.json'), true, 512, JSON_THROW_ON_ERROR);
        }
        return $this->mapping;
    }

    public function version(): string
    {
        return hash('sha256', file_get_contents($this->projectDir.'/data/deree/demo-images.json').file_get_contents(__FILE__));
    }

    /**
     * Resolve the local file path for a venue by name.
     * Returns the absolute path inside data/demo-media/ or null if unmapped/missing.
     */
    public function resolveVenue(string $name): ?string
    {
        $map = $this->load();
        $entry = $map['venues'][$name] ?? null;
        if (!$entry) return null;
        $path = $this->projectDir.'/data/demo-media/'.$entry['file'];
        return is_file($path) ? $path : null;
    }

    /**
     * Resolve the local file path for a facility by name.
     */
    public function resolveFacility(string $name): ?string
    {
        $map = $this->load();
        $entry = $map['facilities'][$name] ?? null;
        if (!$entry) return null;
        $path = $this->projectDir.'/data/demo-media/'.$entry['file'];
        return is_file($path) ? $path : null;
    }

    /**
     * Resolve an event category image. Rotates through available images for the category.
     */
    public function resolveEventCategory(string $categoryName, int $index): ?string
    {
        $map = $this->load();
        $images = $map['event_categories'][$categoryName] ?? null;
        if (!$images) return null;
        $entry = $images[$index % count($images)];
        $path = $this->projectDir.'/data/demo-media/'.$entry['file'];
        return is_file($path) ? $path : null;
    }

    /**
     * Resolve the club logo PNG path by exact name from directory.json.
     */
    public function resolveClub(string $name): ?string
    {
        $slug = $this->clubSlug($name);
        $path = $this->projectDir.'/data/demo-media/clubs/'.$slug.'.png';
        return is_file($path) ? $path : null;
    }

    /**
     * Get image dimensions and size for a local file.
     * @return array{width:int,height:int,size:int,mime:string}|null
     */
    public function imageInfo(string $path): ?array
    {
        if (!is_file($path)) return null;
        $info = @getimagesize($path);
        if (!$info) return null;
        return ['width' => $info[0], 'height' => $info[1], 'size' => filesize($path), 'mime' => $info['mime']];
    }

    /**
     * Copy all demo media subdirectory files to public/uploads/{type}/ using hash comparison.
     */
    public function installAllAssets(): void
    {
        $subdirs = ['venues' => 'venues', 'facilities' => 'facilities', 'events' => 'events', 'clubs' => 'organizers'];
        foreach ($subdirs as $sourceDir => $uploadDir) {
            $sourceBase = $this->projectDir.'/data/demo-media/'.$sourceDir;
            $targetBase = $this->projectDir.'/public/uploads/'.$uploadDir;
            if (!is_dir($sourceBase)) continue;
            if (!is_dir($targetBase)) mkdir($targetBase, 0755, true);
            foreach (glob($sourceBase.'/*') as $source) {
                $target = $targetBase.'/'.basename($source);
                if (!is_file($target)) {
                    if (!copy($source, $target)) {
                        throw new \RuntimeException('Could not install demo image: '.$source);
                    }
                }
            }
        }
    }

    private function clubSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }
}
