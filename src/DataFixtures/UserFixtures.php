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
        $cha = $this->saveUser('charlotte.saidi@outlook.fr', '!', ["ROLE_ADMIN"]);
        $manager->persist($cha);
        $mede = $this->saveUser('mederic.careil@gmail.com', '!', ["ROLE_ADMIN"]);
        $manager->persist($mede);
        $hacked = $this->saveUser('jeMeSuisFaitHacke@merde.fr', '!', ['ROLE_USER']);
        $manager->persist($hacked);

        $manager->flush();
    }

    private function saveUser(string $username, string $password, array $roles): User
    {
        $user = new User();

        $user->setEmail($username)
            ->setPassword($this->passwordHasher->hashPassword(
                $user,
                $password
            ))
            ->setRoles($roles);

        return $user;
    }
}