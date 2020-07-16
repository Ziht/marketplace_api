<?php

namespace Marketplace\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Marketplace\Repository\InvoiceProductRepository")
 * @ORM\Table(name="invoice_product")
 */
class InvoiceProduct
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     */
    private $productId;
    /**
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     */
    private $invoiceId;
    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $amount;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getInvoiceId() . '_' . $this->getProductId();
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
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
}