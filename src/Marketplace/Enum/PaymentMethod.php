<?php
declare(strict_types=1);

namespace Marketplace\Enum;

/**
 * Class PaymentMethod
 * @package Marketplace\Enum
 */
class PaymentMethod
{
    const PAYMENT_METHOD_FAKE = 'fake';

    /**
     * @var array
     */
    public static $values = [
        self::PAYMENT_METHOD_FAKE,
    ];
}