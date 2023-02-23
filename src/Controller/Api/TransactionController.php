<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class TransactionController extends BaseController
{

    public function __construct(
        private TransactionRepository $transactionRepository,
    )
    {}

    /**
     * @Route("/stripe_create", methods={"GET"})
     */
    public function stripeCreate(Request $request): Response
    {
        $user = $this->getUser();

        // Récupération de la transaction en cours s'il y en a une
        $transaction = $this->transactionRepository->findLastOneByUserAndStatus(
            $user,
            [Transaction::TRANSACTION_STATUS_PAYMENT_INTENT]
        );

        if (null === $transaction) {
            $transaction = (new Transaction())
                ->setAmount(100)
                ->setUser($user)
                ->setLabel('Paiement en cours depuis '.$user->getIp())
                ->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_INTENT);
        }

        \Stripe\Stripe::setApiKey($this->getParameter('app.stripe.keys.private'));

        try {
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
            http_response_code(500);

            return $this->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/payment_success", methods={"GET"})
     */
    public function paymentSuccess(Request $request): Response
    {
        $user = $this->getUser();

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

        try {
            $transaction->setPaymentStatus(Transaction::TRANSACTION_STATUS_PAYMENT_SUCCESS);
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

            return $this->json([
                'success' => true,
                'message' => 'Paiement effectué... Merci ! ;)'
            ]);
        } catch (\Error $e) {
            http_response_code(500);
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}