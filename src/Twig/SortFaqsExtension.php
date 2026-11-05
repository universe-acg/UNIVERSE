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
            usort($faqs, static fn($a, $b) => strnatcasecmp($a->getTitle(), $b->getTitle()));

            return $faqs;
        }
    }