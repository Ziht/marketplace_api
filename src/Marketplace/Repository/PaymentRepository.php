<?php
namespace Marketplace\Repository;

use Doctrine\ORM\EntityRepository;
use Marketplace\Entity\Payment;

/**
 * Class PaymentRepository
 * @package Marketplace\Repository
 */
class PaymentRepository extends EntityRepository
{
    /**
     * @param int $invoiceId
     * @return Payment[]
     */
    public function findByInvoiceId(int $invoiceId): array
    {
        return $this->findBy(['invoiceId' => $invoiceId]);
    }
}