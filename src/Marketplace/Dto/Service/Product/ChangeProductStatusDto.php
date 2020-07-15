<?php
declare(strict_types=1);

namespace Marketplace\Dto\Service\Product;

use Core\Dto\Dto;

/**
 * Class ChangeProductStatusDto
 * @package Marketplace\Dto\Service\Product
 */
class ChangeProductStatusDto extends Dto
{
    /**
     * @var int
     */
    protected $invoiceId;

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

}