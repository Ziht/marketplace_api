<?php
declare(strict_types=1);

namespace Marketplace\Bll;

use Marketplace\Entity\Invoice;
use Marketplace\Entity\Payment;
use Marketplace\Entity\Product;
use Marketplace\Enum\PaymentStatus;

/**
 * Class PaymentBll
 * @package Marketplace\Bll
 */
class PaymentBll
{
    /**
     * @param Product[] $products
     * @return float
     */
    public function calcTotalAmount(array $products): float
    {
        $totalAmount = 0.00;
        foreach ($products as $product) {
            $totalAmount += $product->getAmount();
        }

        return $totalAmount;
    }

    /**
     * @param Invoice $invoice
     * @param Payment[] $payments
     * @return float
     */
    public function calcRemainingAmount(Invoice $invoice, array $payments): float
    {
        $paidAmount = 0;
        foreach ($payments as $payment) {
            if (in_array($payment->getStatus(), [PaymentStatus::INVOICE_STATUS_PAID, PaymentStatus::INVOICE_STATUS_SENT])) {
                $paidAmount += $payment->getAmount();
            }
        }

        return $invoice->getTotalAmount() - $paidAmount;
    }

    /**
     * @param Invoice $invoice
     * @param Payment[] $payments
     * @return float
     */
    public function calcRealRemainingAmount(Invoice $invoice, array $payments): float
    {
        $paidAmount = 0;
        foreach ($payments as $payment) {
            if (in_array($payment->getStatus(), [PaymentStatus::INVOICE_STATUS_PAID])) {
                $paidAmount += $payment->getAmount();
            }
        }

        return $invoice->getTotalAmount() - $paidAmount;
    }
}