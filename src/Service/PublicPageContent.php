<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class PublicPageContent
{
    public const LEGACY = '<p>UNIVERSE is a campus-community demonstration. Organization facts are researched from official ACG sources. Student accounts and interactions are fictional. Essential session cookies support sign-in.</p>';

    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir) {}

    public function load(): array
    {
        return json_decode(file_get_contents($this->projectDir.'/data/deree/public-pages.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function mayUpdate(string $current, ?string $installedHash): bool
    {
        return trim($current) === self::LEGACY || ($installedHash !== null && hash_equals($installedHash, hash('sha256', $current)));
    }
}
