<?php
declare(strict_types=1);

namespace Marketplace\Bll;

use Marketplace\Entity\Invoice;
use Marketplace\Entity\Payment;
use Marketplace\Entity\Product;
use Marketplace\Enum\PaymentStatus;

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
            if ($payment->getStatus() === PaymentStatus::INVOICE_STATUS_PAID) {
                $paidAmount += $payment->getAmount();
            }
        }

        return $invoice->getTotalAmount() - $paidAmount;
    }
}