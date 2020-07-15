<?php
namespace Marketplace\Repository;

use Doctrine\ORM\EntityRepository;
use Marketplace\Entity\Payment;

class PaymentRepository extends EntityRepository
{
    /**
     * @param $ids
     * @return Payment[]
     */
    public function findByIds($ids): array
    {
        return $this->findBy(['id' => $ids]);
    }

    /**
     * @param int $invoiceId
     * @return Payment[]
     */
    public function findByInvoiceId(int $invoiceId): array
    {
        return $this->findBy(['invoiceId' => $invoiceId]);
    }
}