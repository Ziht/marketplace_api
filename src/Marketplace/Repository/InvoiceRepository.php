<?php
namespace Marketplace\Repository;

use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
    public function findByIds($ids)
    {
        return $this->findBy(['id' => $ids]);
    }
}