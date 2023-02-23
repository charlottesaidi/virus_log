<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Common\DatedInterface;
use App\Entity\Common\DatedTrait;
use App\Entity\Common\IdInterface;
use App\Entity\Common\IdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("`transaction`")
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction implements DatedInterface, IdInterface
{
    use DatedTrait;
    use IdTrait;

    public const TRANSACTION_STATUS_PAYMENT_INTENT = 'payment_intent';
    public const TRANSACTION_STATUS_PAYMENT_SUCCESS = 'payment_success';
    public const TRANSACTION_STATUS_PAYMENT_FAILURE = 'payment_failure';
    public const TRANSACTION_STATUS_PAYMENT_ABANDONED = 'payment_abandoned';

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $stripeCustomerId = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $paymentMethod = null;

    /**
     * @ORM\Column(length=255)
     
     */
    private $label = null;

    /**
     * @ORM\Column
     */
    private $amount = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $stripePaymentIntentId = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $paymentStatus = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }
    
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): self
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }
}