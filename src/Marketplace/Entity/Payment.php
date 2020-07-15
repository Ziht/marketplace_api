<?php

namespace Marketplace\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marketplace\Enum\PaymentMethod;
use Marketplace\Enum\PaymentStatus;

/**
 * @ORM\Entity(repositoryClass="Marketplace\Repository\PaymentRepository")
 * @ORM\Table(name="payment")
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=11)
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $invoiceId;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $method;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $status;

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @param int $invoiceId
     */
    public function setInvoiceId(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        if (!in_array($method, PaymentMethod::$values)) {
            throw new \InvalidArgumentException("Invalid method");
        }
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        if (!in_array($status, PaymentStatus::$values)) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }
}