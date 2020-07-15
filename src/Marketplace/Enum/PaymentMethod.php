<?php
declare(strict_types=1);

namespace Marketplace\Enum;

class PaymentMethod
{
    const PAYMENT_METHOD_FAKE = 'fake';

    public static $values = [
        self::PAYMENT_METHOD_FAKE,
    ];
}