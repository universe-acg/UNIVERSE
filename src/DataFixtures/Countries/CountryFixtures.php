<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\DataFixtures\Countries;
    
    use App\Entity\Country;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
    use Doctrine\Persistence\ObjectManager;
    
    class CountryFixtures extends Fixture implements FixtureGroupInterface
    {
        
        public static function getGroups(): array
        {
            return ['countries'];
        }
        
        public function load(ObjectManager $manager): void
        {
            
            $countries_en = array(
                'GR' => 'Greece'
            );
            
            foreach ($countries_en as $code => $countryname) {
                $country = new Country();
                $country->setCode($code);
                
                $country->translate('en')->setName($countryname);
                $country->mergeNewTranslations();
                $manager->persist($country);
            }
            
            $manager->flush();
        }
        
    }
