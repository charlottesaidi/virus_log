<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class LogController extends BaseController
{
    /**
     * @Route("/logs", methods={"GET"})
     */
    public function index(): Response
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $user = $this->getUser();

            return $this->json($user);
        } catch (\Throwable $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue');
        }
    }
}