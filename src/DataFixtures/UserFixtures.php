<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {

        $user = new User();
        $user->setName("Boilerplate");
        $user->setEmail("api@boilerplate.com");
        $user->setPassword("12345");
        $password = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);
        $manager->flush();
    }

}
