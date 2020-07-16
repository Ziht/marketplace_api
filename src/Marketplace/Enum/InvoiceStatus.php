<?php
declare(strict_types=1);

namespace Marketplace\Enum;

/**
 * Class InvoiceStatus
 * @package Marketplace\Enum
 */
class InvoiceStatus
{
    const INVOICE_STATUS_SENT = 'SENT';

    const INVOICE_STATUS_PARTIAL = 'PARTIAL';

    const INVOICE_STATUS_PAID = 'PAID';

    const INVOICE_STATUS_CANCELED = 'CANCELED';

    /**
     * @var array
     */
    public static $values = [
        self::INVOICE_STATUS_SENT,
        self::INVOICE_STATUS_PARTIAL,
        self::INVOICE_STATUS_PAID,
        self::INVOICE_STATUS_CANCELED,
    ];

    /**
     * @var array
     */
    public static $pending = [
        self::INVOICE_STATUS_SENT,
        self::INVOICE_STATUS_PARTIAL,
    ];

    /**
     * @var array
     */
    public static $completed = [
        self::INVOICE_STATUS_PAID,
        self::INVOICE_STATUS_CANCELED,
    ];
}