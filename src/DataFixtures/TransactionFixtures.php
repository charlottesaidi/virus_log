<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TransactionFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
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
            $entity = $this->createTransaction($data);
            $user = $this->getReference(UserFixtures::getUserReference((string) $i));
            $entity->setUser($user);
            $entity->setLabel('Paiement depuis '.$user->getMacAddress());
            if(!$user->getEmail()) {
                $entity->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_INTENT);
            }elseif($user->getEncryptionKey()) {
                $entity->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS);
            } else {
                $entity->setPaymentStatus(Transaction::TRANSACTION_USER_FILE_DECRYPTED);
            }
            $manager->persist($entity);
            ++$i;
        }

        $manager->flush();
    }

    private function createTransaction(array $data): Transaction
    {
        $entity = new Transaction();

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
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $stipePaymentId = substr( str_shuffle( $chars ), 0, 24 );

        for ($i = 1; $i < 100; ++$i) {
            yield [
                'amount' => 5000,
                'stripePaymentIntentId' => 'pi_'.$stipePaymentId,
            ];
        }
    }
}