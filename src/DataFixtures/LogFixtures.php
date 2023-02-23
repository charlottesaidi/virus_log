<?php

namespace App\DataFixtures;

use App\Entity\Log;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 100; $i++) {
            $log = $this->saveLog(
                long2ip(rand(0, 4294967295)),
                rand(100, 8000)
            );
            $manager->persist($log);
        }

        $manager->flush();
    }

    private function saveLog(string $ip, int $numberInfectedFiles): Log
    {
        $log = new Log();

        $log->setNumberInfectedFile($numberInfectedFiles)
            ->setIp($ip);

        return $log;
    }
}