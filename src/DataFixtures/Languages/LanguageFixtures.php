<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\DataFixtures\Languages;

use App\Entity\Language;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LanguageFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['languages'];
    }

    public function load(ObjectManager $manager): void
    {

        $languages_en = array(
            'en' => 'English',
        );

        foreach ($languages_en as $code => $languagename) {
            $language = new Language();
            $language->setCode($code);
            $language->translate('en')->setName($languagename);
            $language->mergeNewTranslations();
            $manager->persist($language);
        }

        $manager->flush();
    }
}
