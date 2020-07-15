<?php
declare(strict_types=1);

namespace Marketplace\Dto\Service\Invoice;

use Core\Dto\Dto;

/**
 * Class ChangeInvoiceDto
 * @package Marketplace\Dto\Service\Invoice
 */
class ChangeInvoiceDto extends Dto
{
    /** @var int */
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