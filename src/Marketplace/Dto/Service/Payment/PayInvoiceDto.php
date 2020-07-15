<?php
declare(strict_types=1);

namespace Marketplace\Dto\Service\Payment;

use Core\Dto\Dto;

class PayInvoiceDto extends Dto
{
    /** @var int */
    protected $invoiceId;

    /** @var float */
    protected $paymentAmount;

    /** @var string */
    protected $paymentMethod;

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @return float
     */
    public function getPaymentAmount(): float
    {
        return $this->paymentAmount;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @param int $invoiceId
     */
    public function setInvoiceId(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @param float $paymentAmount
     */
    public function setPaymentAmount(float $paymentAmount): void
    {
        $this->paymentAmount = $paymentAmount;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod(string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }
}