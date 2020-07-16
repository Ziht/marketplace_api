<?php
declare(strict_types=1);

namespace Test\Marketplace;

use Marketplace\Entity\Invoice;
use Marketplace\Entity\Payment;
use Marketplace\Entity\Product;
use Marketplace\Enum\InvoiceStatus;
use Marketplace\Enum\PaymentMethod;
use Marketplace\Enum\PaymentStatus;
use ReflectionException;
use Test\ExtendedTestCase;

/**
 * Class MarketplaceTestCase
 * @package Test\Marketplace
 */
class MarketplaceTestCase extends ExtendedTestCase
{
    /**
     * @param string $name
     * @param int $amount
     * @param int $isEnable
     * @param int $isSold
     * @return Product
     * @throws ReflectionException
     */
    public function getProduct($name = 'default', $amount = 10, $isEnable = 1, $isSold = 0): Product
    {
        $productGenerator = $this->getEntityGenerator(Product::class, 'id');
        /** @var Product $product */
        $product = $productGenerator->current();
        $product->setName($name);
        $product->setAmount($amount);
        $product->setIsEnable($isEnable);
        $product->setIsSold($isSold);
        $productGenerator->next();

        return $product;
    }

    /**
     * @param int $count
     * @param string $name
     * @param int $amount
     * @param int $isEnable
     * @param int $isSold
     * @return Product[]
     * @throws ReflectionException
     */
    public function getProducts($count = 2, $name = 'default', $amount = 10, $isEnable = 1, $isSold = 0): array
    {
        $products = [];
        $productGenerator = $this->getEntityGenerator(Product::class, 'id');
        for ($i = 0; $i < $count; $i++) {
            /** @var Product $product */
            $product = $productGenerator->current();
            $product->setName($name);
            $product->setAmount($amount);
            $product->setIsEnable($isEnable);
            $product->setIsSold($isSold);
            $products[] = $product;
            $productGenerator->next();
        }

        return $products;
    }

    /**
     * @param int $userId
     * @param string $status
     * @param float $totalAmount
     * @return Invoice
     * @throws ReflectionException
     */
    public function getInvoice($userId = 1, $status = InvoiceStatus::INVOICE_STATUS_SENT, $totalAmount = 10.00): Invoice
    {
        $productGenerator = $this->getEntityGenerator(Invoice::class, 'id');
        /** @var Invoice $invoice */
        $invoice = $productGenerator->current();
        $invoice->setUserId($userId);
        $invoice->setTotalAmount($totalAmount);
        $invoice->setStatus($status);
        $productGenerator->next();

        return $invoice;
    }

    /**
     * @param int $count
     * @param int $userId
     * @param string $status
     * @param float $totalAmount
     * @return Invoice[]
     * @throws ReflectionException
     */
    public function getInvoices(
        $count = 2,
        $userId = 1,
        $status = InvoiceStatus::INVOICE_STATUS_SENT,
        $totalAmount = 10.00
    ): array {
        $invoices = [];
        $productGenerator = $this->getEntityGenerator(Invoice::class, 'id');
        for ($i = 0; $i < $count; $i++) {
            /** @var Invoice $invoice */
            $invoice = $productGenerator->current();
            $invoice->setUserId($userId);
            $invoice->setTotalAmount($totalAmount);
            $invoice->setStatus($status);
            $productGenerator->next();
            $invoices[] = $invoice;
            $productGenerator->next();
        }

        return $invoices;
    }

    /**
     * @param int $invoiceId
     * @param float $amount
     * @param string $method
     * @param string $status
     * @return Payment
     * @throws ReflectionException
     */
    public function getPayment(
        $invoiceId = 1,
        $amount = 10.00,
        $method = PaymentMethod::PAYMENT_METHOD_FAKE,
        $status = PaymentStatus::INVOICE_STATUS_SENT
    ): Payment {
        $productGenerator = $this->getEntityGenerator(Payment::class, 'id');
        /** @var Payment $payment */
        $payment = $productGenerator->current();
        $payment->setInvoiceId($invoiceId);
        $payment->setAmount($amount);
        $payment->setMethod($method);
        $payment->setStatus($status);
        $productGenerator->next();

        return $payment;
    }

    /**
     * @param int $count
     * @param int $invoiceId
     * @param float $amount
     * @param string $method
     * @param string $status
     * @return Payment[]
     * @throws ReflectionException
     */
    public function getPayments(
        $count = 2,
        $invoiceId = 1,
        $amount = 10.00,
        $method = PaymentMethod::PAYMENT_METHOD_FAKE,
        $status = PaymentStatus::INVOICE_STATUS_SENT
    ): array {
        $payments = [];
        $productGenerator = $this->getEntityGenerator(Payment::class, 'id');
        for ($i = 0; $i < $count; $i++) {
            /** @var Payment $payment */
            $payment = $productGenerator->current();
            $payment->setInvoiceId($invoiceId);
            $payment->setAmount($amount);
            $payment->setMethod($method);
            $payment->setStatus($status);
            $payments[] = $payment;
            $productGenerator->next();
        }

        return $payments;
    }
}