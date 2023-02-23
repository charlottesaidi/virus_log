<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            ValidatorInterface::class,
            ManagerRegistry::class,
            TokenStorageInterface::class,
            JWTTokenManagerInterface::class,
        ]);
    }

    protected function getUser(): User
    {
        $decodedToken = $this->getJWTTokenManagerInterface()->decode($this->getTokenStorageInterface()->getToken());
        return $this->getManagerRegistry()->getRepository(User::class)->findOneBy(['email' => $decodedToken['username']]);
    }

    protected function getTokenStorageInterface(): TokenStorageInterface
    {
        if (!$this->container->has(TokenStorageInterface::class)) {
            throw new \LogicException('The TokenStorageInterface is not registered in your application.');
        }

        return $this->container->get(TokenStorageInterface::class);
    }

    protected function getJWTTokenManagerInterface(): JWTTokenManagerInterface
    {
        if (!$this->container->has(JWTTokenManagerInterface::class)) {
            throw new \LogicException('The JWTTokenManagerInterface is not registered in your application.');
        }

        return $this->container->get(JWTTokenManagerInterface::class);
    }

    protected function getManagerRegistry(): ManagerRegistry
    {
        if (!$this->container->has(ManagerRegistry::class)) {
            throw new \LogicException('The ManagerRegistry is not registered in your application.');
        }

        return $this->container->get(ManagerRegistry::class);
    }

    /**
     * Returns a failure response with optionnal data (Code: 200)
     * @param mixed $data
     * @param int $status
     * @return JsonResponse
     */
    protected function failure($data = null, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $return = ['status_code' => $status];

        if ($data) {
            $return['data'] = $data;
        }

        return $this->json($return, Response::HTTP_OK);
    }
}
