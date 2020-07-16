<?php
declare(strict_types=1);

namespace Test\Marketplace\Bll;

use Marketplace\Bll\PaymentBll;
use Marketplace\Entity\Invoice;
use Marketplace\Entity\Product;
use Marketplace\Enum\InvoiceStatus;
use Marketplace\Enum\PaymentMethod;
use Marketplace\Enum\PaymentStatus;
use ReflectionException;
use Test\Marketplace\MarketplaceTestCase;

/**
 * Class PaymentBllTest
 * @package Test\Marketplace\Bll
 * @coversDefaultClass \Marketplace\Bll\PaymentBll
 */
class PaymentBllTest extends MarketplaceTestCase
{
    /**
     * @var PaymentBll
     */
    protected $paymentBll;

    /**
     * @covers ::calcTotalAmount
     * @dataProvider calcTotalAmountDataProvider
     * @param Product[] $products
     * @param float $expectedSum
     */
    public function testCalcTotalAmount(array $products, float $expectedSum): void
    {
        $resultSum = $this->paymentBll->calcTotalAmount($products);
        $this->assertEquals($expectedSum, $resultSum);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function calcTotalAmountDataProvider(): array
    {
        $products = $this->getProducts(2, 'product_name', 10.00);
        $sum = 20.00;
        return [
            ['products' => $products, 'sum' => $sum],
        ];
    }

    /**
     * @covers ::calcRemainingAmount
     * @dataProvider calcRemainingAmountDataProvider
     * @param Invoice $invoice
     * @param Product[] $products
     * @param float $expectedRemainingAmount
     */
    public function testCalcRemainingAmount(Invoice $invoice, array $products, float $expectedRemainingAmount): void
    {
        $resultRemainingAmount = $this->paymentBll->calcRemainingAmount($invoice, $products);
        $this->assertEquals($expectedRemainingAmount, $resultRemainingAmount);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function calcRemainingAmountDataProvider(): array
    {
        $invoiceFirst = $this->getInvoice(1, InvoiceStatus::INVOICE_STATUS_SENT, 20.00);
        $paymentsFirst = [
            $this->getPayment($invoiceFirst->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_SENT),
            $this->getPayment($invoiceFirst->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_PAID),
        ];

        $invoiceSecond = $this->getInvoice(1, InvoiceStatus::INVOICE_STATUS_SENT, 20.00);
        $paymentsSecond = [
            $this->getPayment($invoiceSecond->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_SENT),
            $this->getPayment($invoiceSecond->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_CANCELED),
        ];

        return [
            [$invoiceFirst, $paymentsFirst, 0.00],
            [$invoiceSecond, $paymentsSecond, 10.00],
        ];
    }

    /**
     * @covers ::calcRealRemainingAmount
     * @dataProvider calcRealRemainingAmountDataProvider
     * @param Invoice $invoice
     * @param Product[] $products
     * @param float $expectedRemainingAmount
     */
    public function testCalcRealRemainingAmount(Invoice $invoice, array $products, float $expectedRemainingAmount): void
    {
        $resultRemainingAmount = $this->paymentBll->calcRealRemainingAmount($invoice, $products);
        $this->assertEquals($expectedRemainingAmount, $resultRemainingAmount);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function calcRealRemainingAmountDataProvider(): array
    {
        $invoiceFirst = $this->getInvoice(1, InvoiceStatus::INVOICE_STATUS_PAID, 20.00);
        $paymentsFirst = [
            $this->getPayment($invoiceFirst->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_PAID),
            $this->getPayment($invoiceFirst->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_PAID),
        ];

        $invoiceSecond = $this->getInvoice(1, InvoiceStatus::INVOICE_STATUS_SENT, 20.00);
        $paymentsSecond = [
            $this->getPayment($invoiceSecond->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_SENT),
            $this->getPayment($invoiceSecond->getId(), 10.00, PaymentMethod::PAYMENT_METHOD_FAKE, PaymentStatus::INVOICE_STATUS_CANCELED),
        ];

        return [
            [$invoiceFirst, $paymentsFirst, 0.00],
            [$invoiceSecond, $paymentsSecond, 20.00],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->paymentBll = new PaymentBll();

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        unset($this->paymentBll);

        parent::tearDown();
    }
}