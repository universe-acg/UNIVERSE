<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Twig;
    
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;
    
    class AppExtension extends AbstractExtension
    {
        
        public function getFilters(): array
        {
            return [
                new TwigFilter('getLangFlag', [$this, 'getLangFlag']),
                new TwigFilter('getLangName', [$this, 'getLangName']),
            ];
        }
        
        public function getLangFlag()
        {
            return "us";
        }
        
        public function getLangName()
        {
            return "English";
        }
    }
