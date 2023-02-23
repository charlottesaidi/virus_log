<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {}

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        try {
            $ip = $request->getClientIp();
            $user = new User();

            $data = json_decode($request->getContent(), true);

            $user->setEmail($data['email'])
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $data['key']
                ))
                ->setIp($ip)
                ->setRoles(['ROLE_USER']);

            $this->userRepository->save($user, true);

            return $this->json($user);
        } catch(\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }
}