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
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param mixed $invoiceId
     */
    public function setInvoiceId($invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }
}