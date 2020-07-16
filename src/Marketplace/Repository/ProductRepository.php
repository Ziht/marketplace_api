<?php
namespace Marketplace\Repository;

use Doctrine\ORM\EntityRepository;
use Marketplace\Entity\Product;

/**
 * Class ProductRepository
 * @package Marketplace\Repository
 */
class ProductRepository extends EntityRepository
{
    /**
     * @param $ids
     * @return Product[]
     */
    public function findByIds($ids)
    {
        return $this->findBy(['id' => $ids]);
    }
}