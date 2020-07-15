<?php
declare(strict_types=1);

namespace Marketplace\Service;

use Core\Exception\ValidationException;
use Core\Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Marketplace\Bll\PaymentBll;
use Marketplace\Dto\Service\Invoice\ChangeInvoiceDto;
use Marketplace\Dto\Service\Invoice\CreateInvoiceDto;
use Marketplace\Dto\Service\Invoice\LinkProductsDto;
use Marketplace\Dto\Service\Product\HoldProductsDto;
use Marketplace\Entity\Invoice;
use Marketplace\Entity\InvoiceProduct;
use Marketplace\Entity\Product;
use Marketplace\Enum\InvoiceStatus;
use Marketplace\Repository\InvoiceProductRepository;
use Marketplace\Repository\InvoiceRepository;
use Marketplace\Repository\PaymentRepository;
use Marketplace\Repository\ProductRepository;
use ReflectionException;

class InvoiceService
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
     * @var UserService
     */
    protected $userService;

    /**
     * @var PaymentBll
     */
    protected $paymentBll;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var InvoiceProductRepository
     */
    protected $invoiceProductRepository;

    public function __construct(
        EntityManager $entityManager,
        ProductRepository $productRepository,
        InvoiceRepository $invoiceRepository,
        PaymentRepository $paymentRepository,
        InvoiceProductRepository $invoiceProductRepository,
        UserService $userService,
        ProductService $productService,
        PaymentBll $paymentBll
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;
        $this->invoiceProductRepository = $invoiceProductRepository;

        $this->userService = $userService;
        $this->productService = $productService;

        $this->paymentBll = $paymentBll;
    }

    /**
     * @param CreateInvoiceDto $invoiceDto
     * @return Result
     * @throws ReflectionException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function createInvoice(CreateInvoiceDto $invoiceDto): Result
    {
        $result = new Result();
        try {
            if ($invoiceDto->isNeedValidate()) {
                $this->validateCreateInvoiceDto($invoiceDto);
            }
            /** @var Product[] $products */
            $products = $this->productRepository->findByIds($invoiceDto->getProductIds());
            $invoice = new Invoice();
            $invoice->setUserId($this->userService->getCurrentUserId());
            $invoice->setStatus(InvoiceStatus::INVOICE_STATUS_SENT);
            $invoice->setTotalAmount($this->paymentBll->calcTotalAmount($products));
            $productsDto = new HoldProductsDto([], false);
            $productsDto->setProductIds($invoiceDto->getProductIds());
            $this->productService->holdProducts($productsDto);
            $this->entityManager->persist($invoice);
            $this->entityManager->flush();
            $productIds = array_map(function (Product $product) {
                return $product->getId();
            }, $products);
            $linkProductsDto = new LinkProductsDto([], false);
            $linkProductsDto->setInvoiceId($invoice->getId());
            $linkProductsDto->setProductIds($productIds);
            $this->linkProducts($linkProductsDto);
            $result->setData(['invoiceId' => $invoice->getId()]);
        } catch (ValidationException $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;
    }

    /**
     * @param CreateInvoiceDto $dto
     * @throws Exception
     */
    public function validateCreateInvoiceDto(CreateInvoiceDto $dto): void
    {
        $holdProductsDto = new HoldProductsDto();
        $holdProductsDto->setProductIds($dto->getProductIds());
        $this->productService->validateHoldProducts($holdProductsDto);
        /** @var Product[] $products */
        $products = $this->productRepository->findByIds($holdProductsDto->getProductIds());
        foreach ($products as $product) {
            if (!$product->getIsEnable() || $product->getIsSold()) {
                throw new ValidationException('Product unavailable or already sold.');
            }
        }
    }

    /**
     * @param LinkProductsDto $linkProductsDto
     * @return Result
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function linkProducts(LinkProductsDto $linkProductsDto): Result
    {
        $result = new Result();
        try {
            if ($linkProductsDto->isNeedValidate()) {
                $this->validateLinkProducts($linkProductsDto);
            }
            $resultKeys = [];
            $products = $this->productRepository->findByIds($linkProductsDto->getProductIds());
            foreach ($products as $product) {
                $invoiceProduct = new InvoiceProduct();
                $invoiceProduct->setInvoiceId($linkProductsDto->getInvoiceId());
                $invoiceProduct->setProductId($product->getId());
                $invoiceProduct->setAmount($product->getAmount());
                $this->entityManager->persist($invoiceProduct);
                $resultKeys[] = $invoiceProduct->getKey();
            }
            $this->entityManager->flush();
            $result->setData($resultKeys);
        } catch (ValidationException $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;
    }

    /**
     * @param LinkProductsDto $linkProductsDto
     * @throws ValidationException
     */
    public function validateLinkProducts(LinkProductsDto $linkProductsDto): void
    {
        $invoiceProducts = $this->invoiceProductRepository->findBy(
            [
                'invoiceId' => $linkProductsDto->getInvoiceId(),
                'productId' => $linkProductsDto->getProductIds(),
            ]
        );
        if ($invoiceProducts) {
            throw new ValidationException('Link already exists.');
        }
    }

    /**
     * @param ChangeInvoiceDto $changeInvoiceDto
     * @return Result
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function changeInvoiceStatus(ChangeInvoiceDto $changeInvoiceDto): Result
    {
        $result = new Result();
        try {
            if ($changeInvoiceDto->isNeedValidate()) {
                $this->validateChangeInvoiceStatus($changeInvoiceDto);
            }
            /** @var Invoice $invoice */
            $invoice = $this->invoiceRepository->find($changeInvoiceDto->getInvoiceId());
            $payments = $this->paymentRepository->findByInvoiceId($changeInvoiceDto->getInvoiceId());
            $remainingAmount = $this->paymentBll->calcRemainingAmount($invoice, $payments);
            if ($remainingAmount === 0.0) {
                $invoice->setStatus(InvoiceStatus::INVOICE_STATUS_PAID);
            } elseif ($remainingAmount === $invoice->getTotalAmount()) {
                $invoice->setStatus(InvoiceStatus::INVOICE_STATUS_SENT);
            } elseif ($remainingAmount < $invoice->getTotalAmount()) {
                $invoice->setStatus(InvoiceStatus::INVOICE_STATUS_PARTIAL);
            }
            $this->entityManager->persist($invoice);
            $this->entityManager->flush();
            $result->setData(['invoiceId' => $invoice->getId()]);
        } catch (ValidationException $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;


    }

    /**
     * @param ChangeInvoiceDto $changeInvoiceDto
     * @throws ValidationException
     */
    public function validateChangeInvoiceStatus(ChangeInvoiceDto $changeInvoiceDto): void
    {
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->find($changeInvoiceDto->getInvoiceId());
        if (!$invoice) {
            throw new ValidationException('Invoice not found.');
        }
    }
}