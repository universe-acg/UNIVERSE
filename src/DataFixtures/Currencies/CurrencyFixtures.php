<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\DataFixtures\Currencies;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['currencies'];
    }

    public function load(ObjectManager $manager)
    {

        $currencies = array(
            "EUR" => "€",
        );

        foreach ($currencies as $ccy => $symbol) {
            $currency = new Currency();
            $currency->setCcy($ccy);
            $currency->setSymbol($symbol);
            $manager->persist($currency);
        }

        $manager->flush();
    }
}
