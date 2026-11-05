<?php
namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PublicUiExtension extends AbstractExtension
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir) {}

    public function getFunctions(): array
    {
        return [new TwigFunction('public_image', [$this, 'image'])];
    }

    public function image(?string $path): string
    {
        $path = ltrim($path ?? '', '/');
        if ($path !== '' && !str_contains($path, '..') && is_file($this->projectDir.'/public/'.$path)) {
            return '/'.$path;
        }
        return '/images/universe-placeholder.svg';
    }
}
