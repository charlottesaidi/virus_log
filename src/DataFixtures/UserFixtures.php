<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $cha = $this->saveUser('127.0.0.1', '!', ["ROLE_ADMIN"]);
        $manager->persist($cha);
        $hacked = $this->saveUser('158.242.1.128', '!', ['ROLE_USER']);
        $manager->persist($hacked);

        $manager->flush();
    }

    private function saveUser(string $username, string $password, array $roles): User
    {
        $user = new User();

        $user->setIp($username)
            ->setPassword($this->passwordHasher->hashPassword(
                $user,
                $password
            ))
            ->setRoles($roles);

        return $user;
    }
}