<?php

namespace App\DataFixtures;

use App\Entity\Log;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class LogFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function load(ObjectManager $manager): void
    {
        $i = 1;
        foreach ($this->getData() as $data) {
            $entity = $this->createLog($data);
            $user = $this->getReference(UserFixtures::getUserReference((string) $i));
            $entity->setUser($user);
            $entity->setIp($user->getIp());
            $manager->persist($entity);
            ++$i;
        }

        $manager->flush();
    }

    private function createLog(array $data): Log
    {
        $entity = new Log();

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($entity, $key)) {
                $propertyAccessor->setValue($entity, $key, $value);
            }
        }

        return $entity;
    }

    private function getData(): iterable
    {
        for ($i = 1; $i < 100; ++$i) {
            yield [
                'numberInfectedFile' => rand(100, 8000)
            ];
        }
    }
}