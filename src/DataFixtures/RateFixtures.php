<?php
namespace App\DataFixtures;

use App\Entity\Rate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $rate = new Rate();
        $rate->setRate(1000);
        $rate->setUnit($this->getReference('volume_unit'));
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setRate(5);
        $rate->setUnit($this->getReference('weight_unit'));
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setRate(3);
        $rate->setUnit($this->getReference('declared_value_unit'));
        $manager->persist($rate);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UnitFixtures::class,
        ];
    }
}