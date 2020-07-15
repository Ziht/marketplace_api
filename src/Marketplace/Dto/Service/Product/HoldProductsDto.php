<?php
declare(strict_types=1);

namespace Marketplace\Dto\Service\Product;

use Core\Dto\Dto;

/**
 * Class HoldProductsDto
 * @package Marketplace\Dto
 */
class HoldProductsDto extends Dto
{
    /**
     * @var int[]
     */
    protected $productIds;

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    /**
     * @param int[] $productIds
     */
    public function setProductIds(array $productIds): void
    {
        $this->productIds = $productIds;
    }
}