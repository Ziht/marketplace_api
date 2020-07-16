<?php
declare(strict_types=1);

namespace Core\PaymentGateway;

/**
 * Class PaymentGatewayFactory
 * @package Core\PaymentGateway
 */
class PaymentGatewayFactory
{
    /**
     * @var PaymentGateway
     */
    protected $paymentGateway;

    /**
     * @var FakePaymentGateway
     */
    protected $fakePaymentGateway;

    /**
     * PaymentGatewayFactory constructor.
     * @param FakePaymentGateway $fakePaymentGateway
     */
    public function __construct(FakePaymentGateway $fakePaymentGateway, PaymentGateway $paymentGateway)
    {
        $this->fakePaymentGateway = $fakePaymentGateway;
    }

    /**
     * @param string $paymentMethod
     * @return PaymentGatewayInterface
     */
    public function build(string $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'fake':
                $paymentGateway = $this->fakePaymentGateway;
                break;
            default:
                $paymentGateway = $this->paymentGateway;
        }

        return $paymentGateway;
    }
}