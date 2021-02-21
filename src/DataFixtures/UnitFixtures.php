<?php
namespace App\DataFixtures;

use App\Entity\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UnitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $weightUnit = new Unit();
        $weightUnit->setName('weight');
        $this->setReference('weight_unit', $weightUnit);
        $manager->persist($weightUnit);

        $declaredValueUnit = new Unit();
        $declaredValueUnit->setName('value');
        $this->setReference('declared_value_unit', $declaredValueUnit);
        $manager->persist($declaredValueUnit);

        $volumeUnit = new Unit();
        $volumeUnit->setName('volume');
        $this->setReference('volume_unit', $volumeUnit);
        $manager->persist($volumeUnit);

        $manager->flush();
    }
}