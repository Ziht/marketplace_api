<?php
declare(strict_types=1);

namespace Marketplace\Service;

use Core\Exception\ValidationException;
use Core\Result;
use Doctrine\ORM\EntityManager;
use Exception;
use Marketplace\Dto\Service\Product\ChangeProductStatusDto;
use Marketplace\Dto\Service\Product\HoldProductsDto;
use Marketplace\Entity\Invoice;
use Marketplace\Entity\InvoiceProduct;
use Marketplace\Entity\Product;
use Marketplace\Enum\InvoiceStatus;
use Marketplace\Repository\InvoiceProductRepository;
use Marketplace\Repository\InvoiceRepository;
use Marketplace\Repository\ProductRepository;

class ProductService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var InvoiceProductRepository
     */
    protected $invoiceProductRepository;

    public function __construct(
        EntityManager $entityManager,
        ProductRepository $productRepository,
        InvoiceRepository $invoiceRepository,
        InvoiceProductRepository $invoiceProductRepository
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceProductRepository = $invoiceProductRepository;
    }

    /**
     * @param HoldProductsDto $holdProductsDto
     * @return Result
     */
    public function holdProducts(HoldProductsDto $holdProductsDto): Result
    {
        $result = new Result();
        try {
            if ($holdProductsDto->isNeedValidate()) {
                $this->validateHoldProducts($holdProductsDto);
            }
            $products = $this->productRepository->findByIds($holdProductsDto->getProductIds());
            foreach ($products as $product) {
                $product->setIsEnable(false);
                $this->entityManager->persist($product);
            }
            $this->entityManager->flush();
            $result->setData($holdProductsDto->getProductIds());
        } catch (Exception $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;
    }

    /**
     * @param HoldProductsDto $holdProductsDto
     * @throws Exception
     */
    public function validateHoldProducts(HoldProductsDto $holdProductsDto): void
    {
        if (!$holdProductsDto->getProductIds()) {
            throw new ValidationException('Product IDs not set.');
        }
        /** @var Product[] $products */
        $products = $this->productRepository->findByIds($holdProductsDto->getProductIds());
        if (!$products) {
            throw new ValidationException('No products found.');
        }
        if (count($holdProductsDto->getProductIds()) !== count($products)) {
            throw new ValidationException('Some products not found.');
        }
    }

    public function changeProductsStatus(ChangeProductStatusDto $changeProductStatusDto): Result
    {
        $result = new Result();
        try {
            if ($changeProductStatusDto->isNeedValidate()) {
                $this->validateChangeProductsStatus($changeProductStatusDto);
            }
            /** @var Invoice $invoice */
            $invoice = $this->invoiceRepository->find($changeProductStatusDto->getInvoiceId());
            /** @var InvoiceProduct[] $invoiceProducts */
            $invoiceProducts = $this->invoiceProductRepository->findBy(['invoiceId' => $changeProductStatusDto->getInvoiceId()]);
            $productIds = array_map(function (InvoiceProduct $invoiceProduct) {
                return $invoiceProduct->getProductId();
            }, $invoiceProducts);
            /** @var Product[] $products */
            $products = $this->productRepository->findByIds($productIds);
            if ($invoice->getStatus() === InvoiceStatus::INVOICE_STATUS_PAID) {
                foreach ($products as $product) {
                    $product->setIsSold(true);
                    $this->entityManager->persist($product);
                }
                $this->entityManager->flush();
            }
        } catch (ValidationException $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;
    }

    public function validateChangeProductsStatus(ChangeProductStatusDto $changeProductStatusDto): void
    {
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->find($changeProductStatusDto->getInvoiceId());
        if (!$invoice) {
            throw new ValidationException('Invoice not found.');
        }
        /** @var InvoiceProduct[] $invoiceProducts */
        $invoiceProducts = $this->invoiceProductRepository->findBy(['invoiceId' => $changeProductStatusDto->getInvoiceId()]);
        if (!$invoiceProducts) {
            throw new ValidationException('No related products.');
        }
        $productIds = array_map(function (InvoiceProduct $invoiceProduct) {
            return $invoiceProduct->getProductId();
        }, $invoiceProducts);
        /** @var Product[] $products */
        $products = $this->productRepository->findByIds($productIds);
        foreach ($products as $product) {
            if ($product->getIsSold()) {
                throw new ValidationException('Product already sold.');
            }
        }
    }
}