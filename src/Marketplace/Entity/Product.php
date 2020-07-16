<?php

namespace Marketplace\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Marketplace\Repository\ProductRepository")
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=11)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $isEnable;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $isSold;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return bool
     */
    public function getIsEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @param bool $isEnable
     */
    public function setIsEnable(bool $isEnable): void
    {
        $this->isEnable = $isEnable;
    }

    /**
     * @return bool
     */
    public function getIsSold(): bool
    {
        return $this->isSold;
    }

    /**
     * @param bool $isSold
     */
    public function setIsSold(bool $isSold): void
    {
        $this->isSold = $isSold;
    }
}