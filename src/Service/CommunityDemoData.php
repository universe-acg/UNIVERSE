<?php
namespace App\Service;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
final class CommunityDemoData
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir) {}
    public function load(): array { return json_decode(file_get_contents($this->projectDir.'/data/deree/community-demo.json'),true,512,JSON_THROW_ON_ERROR); }
    public function version(): string { return hash('sha256',file_get_contents($this->projectDir.'/data/deree/community-demo.json').file_get_contents(__FILE__)); }
    public static function points(string $username): int
    {
        $value=hexdec(substr(hash('sha256',$username),0,4));
        return str_starts_with($username,'student-') ? 1+($value%4) : (str_starts_with($username,'organizer-') ? 5+($value%51) : 0);
    }
    public function installAssets(): void
    {
        foreach (glob($this->projectDir.'/data/demo-media/*') as $source) {
            if (is_dir($source)) continue;
            $target=$this->projectDir.'/public/uploads/'.(str_ends_with($source,'.webm')?'videos/':'reels/').basename($source);
            if (!is_dir(dirname($target))) mkdir(dirname($target),0755,true);
            if (!is_file($target) || hash_file('sha256',$source)!==hash_file('sha256',$target)) {
                if (!copy($source,$target)) throw new \RuntimeException('Demo media could not be installed.');
            }
            if (str_ends_with($source,'.svg')) {
                $liveTarget=$this->projectDir.'/public/uploads/lives/'.basename($source); if (!is_dir(dirname($liveTarget))) mkdir(dirname($liveTarget),0755,true); copy($source,$liveTarget);
            }
        }
    }
}
