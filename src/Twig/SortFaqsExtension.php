<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Twig;
    
    
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;
    
    class SortFaqsExtension extends AbstractExtension
    {
        public function getFilters()
        {
            return [
                new TwigFilter('sortFaqs', [$this, 'sortFaqs']),
            ];
        }
        
        public function sortFaqs($faqs)
        {
            // Custom sorting logic here
            usort($faqs, static function ($a, $b) {
                $numberA = (int)strtok($a->getTitle(), '.');
                $numberB = (int)strtok($b->getTitle(), '.');
                return $numberA - $numberB;
            });
            
            return $faqs;
        }
    }