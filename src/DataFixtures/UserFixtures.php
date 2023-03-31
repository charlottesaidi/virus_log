<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $fakerFactory;

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public static function getAdminReference(string $key): string
    {
        return User::class . '_ADMIN_' . $key;
    }

    public static function getUserReference(string $key): string
    {
        return User::class . '_USER_' . $key;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAdminData() as $data) {
            $entity = $this->createUser($data);
            $manager->persist($entity);
            $this->addReference(self::getAdminReference($entity->getIp()), $entity);
        }

        $i = 0;
        foreach ($this->getData() as $data) {
            $entity = $this->createUser($data);
            $manager->persist($entity);
            $this->addReference(self::getUserReference((string) $i), $entity);
            ++$i;
        }

        $manager->flush();
    }

    private function createUser(array $data): User
    {
        $entity = new User();

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        if ($plainPassword = $data['plainPassword'] ?? null) {
            $password = $this->passwordHasher->hashPassword($entity, $plainPassword);
            $data['password'] = $password;
            unset($data['plainPassword']);
        }

        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($entity, $key)) {
                $propertyAccessor->setValue($entity, $key, $value);
            }
        }

        return $entity;
    }

    private function getAdminData(): iterable
    {
        $faker = $this->fakerFactory;
        yield [
            'email' => 'charlotte.saidi@outlook.fr',
            'plainPassword' => '!',
            'decryptId' => 'null',
            'ip' => '127.0.0.1',
            'macAddress' => $faker->macAddress(),
            'roles' => ['ROLE_ADMIN']
        ];
    }

    private function getData(): iterable
    {
        $faker = $this->fakerFactory;

        for ($i = 0; $i < 100; ++$i) {
            $uuid = Uuid::v4();
            $email = match ($i % 5) {
                0 => null,
                3, 1, 2, 4 => $faker->email()
            };

            $encryptionKey = match ($i % 5) {
                0, 3 => $faker->regexify('[A-Za-z0-9]{20}'),
                1, 2, 4 => null
            };

            $data = [
                'email' => $email,
                'plainPassword' => $uuid,
                'decryptId' => $uuid,
                'encryptionKey' => $encryptionKey,
                'macAddress' => $faker->macAddress(),
                'ip' => $faker->ipv4(),
                'roles' => ['ROLE_USER']
            ];
            yield $data;
        }
    }
}