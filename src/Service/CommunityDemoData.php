<?php
namespace App\Service;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
final class CommunityDemoData
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir) {}
    public function load(): array { return json_decode(file_get_contents($this->projectDir.'/data/deree/community-demo.json'),true,512,JSON_THROW_ON_ERROR); }
    // Asset-only maintenance must not reapply editable community fixtures.
    public function version(): string { return hash('sha256', file_get_contents($this->projectDir.'/data/deree/community-demo.json').':community-v1'); }
    public function isCompatibleLegacyVersion(string $version): bool {
        return in_array($version, ['047f0b4e15cfae81a0ddb386999649c84ea0f8192b3d39339d75220d3ddc9266', '78f262ddfb238415c5e8b94cbab13affc7e7d9886bd148dd8e58134d4273c762'], true) && hash_file('sha256', $this->projectDir.'/data/deree/community-demo.json') === 'b15f853845bab33071fa5374df2aecb5a64fd9ef0c68c591ba7901b1102a30d0';
    }
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
