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
    private $fakerFactory;

    public function __construct() {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

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
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $stipePaymentId = substr( str_shuffle( $chars ), 0, 24 );

            $entity = $this->createTransaction($data);
            $user = $this->getReference(UserFixtures::getUserReference((string) $i));
            $entity->setUser($user);
            $entity->setLabel('Paiement depuis '.$user->getMacAddress());
            if(!$user->getEmail()) {
                $entity->setStripePaymentIntentId(null);
                $entity->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_INTENT);
            }elseif($user->getEncryptionKey()) {
                $entity->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS);
                $entity->setPaymentMethod(json_encode($this->getCardData()));
                $entity->setStripePaymentIntentId($stipePaymentId);
            } else {
                $entity->setPaymentStatus(Transaction::TRANSACTION_USER_FILE_DECRYPTED);
                $entity->setPaymentMethod(json_encode($this->getCardData()));
                $entity->setStripePaymentIntentId($stipePaymentId);
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
        for ($i = 1; $i < 100; ++$i) {
            yield [
                'amount' => 5000
            ];
        }
    }

    private function getCardData(): array
    {
        $faker = $this->fakerFactory;

        $brand = $faker->creditCardType();
        $cardNumber = $faker->creditCardNumber($brand, true);

        return [
            'card' => [
                'country' => 'FR',
                'brand' => $brand,
                'exp_month' => $faker->creditCardExpirationDateString(true, 'm'),
                'exp_year' => $faker->creditCardExpirationDateString(true, 'Y'),
                'last4' => substr($cardNumber, -4)
            ]
        ];
    }
}