<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@tiny-parcel.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,'123pwd'));
        // $user->setApiToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        // Hardcoded for easy accessing, otherwise would need to go to the table in the DB
        $user->setApiToken('_WvpbpJOns9ZxdOIuxWMTsFFj0AdZY0KubskvSUhIb0');
        $manager->persist($user);

        $manager->flush();
    }
}
