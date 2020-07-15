<?php
declare(strict_types=1);

namespace Marketplace\Dto\Service\Invoice;

use Core\Dto\Dto;

/**
 * Class CreateInvoiceDto
 * @package Marketplace\Dto
 */
class CreateInvoiceDto extends Dto
{
    /** @var int */
    protected $invoiceId;

    /** @var int[] */
    protected $productIds;

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    /**
     * @param int $invoiceId
     */
    public function setInvoiceId(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @param int[] $productIds
     */
    public function setProductIds(array $productIds): void
    {
        $this->productIds = $productIds;
    }
}