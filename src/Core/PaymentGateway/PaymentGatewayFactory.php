<?php
declare(strict_types=1);

namespace Core\PaymentGateway;

class PaymentGatewayFactory
{
    public function build(string $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'fake':

        }
    }
}