<?php
declare(strict_types=1);

namespace Marketplace\Enum;

/**
 * Class PaymentStatus
 * @package Marketplace\Enum
 */
class PaymentStatus
{
    const INVOICE_STATUS_SENT = 'SENT';

    const INVOICE_STATUS_PAID = 'PAID';

    const INVOICE_STATUS_CANCELED = 'CANCELED';

    /**
     * @var array
     */
    public static $values = [
        self::INVOICE_STATUS_SENT,
        self::INVOICE_STATUS_PAID,
        self::INVOICE_STATUS_CANCELED,
    ];

    /**
     * @var array
     */
    public static $pending = [
        self::INVOICE_STATUS_SENT,
    ];

    /**
     * @var array
     */
    public static $completed = [
        self::INVOICE_STATUS_PAID,
    ];
}