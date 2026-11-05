<?php
namespace App\Twig;
use App\Service\MenuNavigation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
final class MenuExtension extends AbstractExtension
{
    public function __construct(private MenuNavigation $navigation) {}
    public function getFunctions(): array {return [new TwigFunction('navigation_menu',[$this->navigation,'menu'])];}
}
