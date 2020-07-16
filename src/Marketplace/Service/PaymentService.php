<?php
declare(strict_types=1);

namespace Marketplace\Service;

use Core\Exception\ValidationException;
use Core\HttpClient\HttpMethod;
use Core\Mq\Rabbitmq;
use Core\PaymentGateway\PaymentGatewayFactory;
use Core\Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Marketplace\Bll\PaymentBll;
use Marketplace\Dto\Service\Invoice\ChangeInvoiceDto;
use Marketplace\Dto\Service\Payment\PayInvoiceDto;
use Marketplace\Dto\Service\Product\ChangeProductStatusDto;
use Marketplace\Entity\Invoice;
use Marketplace\Entity\Payment;
use Marketplace\Enum\InvoiceStatus;
use Marketplace\Enum\PaymentMethod;
use Marketplace\Enum\PaymentStatus;
use Marketplace\Repository\InvoiceRepository;
use Marketplace\Repository\PaymentRepository;

/**
 * Class PaymentService
 * @package Marketplace\Service
 */
class PaymentService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var PaymentGatewayFactory
     */
    protected $paymentGatewayFactory;

    /**
     * @var Rabbitmq
     */
    protected $rabbitmq;

    /**
     * @var PaymentBll
     */
    protected $paymentBll;

    /**
     * PaymentService constructor.
     * @param EntityManager $entityManager
     * @param InvoiceRepository $invoiceRepository
     * @param PaymentRepository $paymentRepository
     * @param UserService $userService
     * @param InvoiceService $invoiceService
     * @param ProductService $productService
     * @param PaymentGatewayFactory $paymentGatewayFactory
     * @param PaymentBll $paymentBll
     * @param Rabbitmq $rabbitmq
     */
    public function __construct(
        EntityManager $entityManager,
        InvoiceRepository $invoiceRepository,
        PaymentRepository $paymentRepository,
        UserService $userService,
        InvoiceService $invoiceService,
        ProductService $productService,
        PaymentGatewayFactory $paymentGatewayFactory,
        PaymentBll $paymentBll,
        Rabbitmq $rabbitmq
    ) {
        $this->entityManager = $entityManager;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;

        $this->userService = $userService;
        $this->invoiceService = $invoiceService;
        $this->productService = $productService;

        $this->paymentGatewayFactory = $paymentGatewayFactory;
        $this->paymentBll = $paymentBll;
        $this->rabbitmq = $rabbitmq;
    }

    /**
     * @param PayInvoiceDto $payInvoiceDto
     * @return Result
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function startPayInvoice(PayInvoiceDto $payInvoiceDto): Result
    {
        $result = new Result();
        try {
            if ($payInvoiceDto->isNeedValidate()) {
                $this->validatePayInvoiceDto($payInvoiceDto);
            }
            $payment = new Payment();
            $payment->setInvoiceId($payInvoiceDto->getInvoiceId());
            $payment->setMethod($payInvoiceDto->getPaymentMethod());
            $payment->setAmount($payInvoiceDto->getPaymentAmount());
            $payment->setStatus(PaymentStatus::INVOICE_STATUS_SENT);
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
            $this->rabbitmq->execute(
                'defaultQueue',
                [
                    'class' => self::class,
                    'method' => 'payInvoice',
                    'data' => [
                        'paymentId' => $payment->getId()
                    ]
                ]
            );
            $result->setData(['paymentStatus' => $payment->getStatus()]);
        } catch (ValidationException $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;
    }

    /**
     * @param PayInvoiceDto $dto
     * @throws Exception
     */
    public function validatePayInvoiceDto(PayInvoiceDto $dto): void
    {
        if (!$dto->getInvoiceId()) {
            throw new ValidationException('Invoice ID not set. Invoice ID: ' . $dto->getInvoiceId());
        }
        if (!$dto->getPaymentAmount()) {
            throw new ValidationException('Payment amount not established.');
        }
        if (!$dto->getPaymentMethod() || !in_array($dto->getPaymentMethod(), PaymentMethod::$values)) {
            throw new ValidationException('Incorrect payment method(' . $dto->getPaymentMethod() . ').');
        }
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->find($dto->getInvoiceId());
        if ($invoice === null) {
            throw new ValidationException('No invoice found. Invoice ID: ' . $dto->getInvoiceId());
        }
        if (in_array($invoice->getStatus(), InvoiceStatus::$completed)) {
            throw new ValidationException('Invoice is sold or cancelled. Invoice status: ' . $invoice->getStatus());
        }
        $payments = $this->paymentRepository->findByInvoiceId($dto->getInvoiceId());
        $remainingAmount = $this->paymentBll->calcRemainingAmount($invoice, $payments);
        if ($dto->getPaymentAmount() > $remainingAmount) {
            throw new ValidationException('Amount exceeds remaining payment amount. You need to pay ' . $remainingAmount . '.');
        }
    }

    /**
     * @param array $data
     * @return Result
     */
    public function payInvoice(array $data): Result
    {
        $result = new Result();
        $this->entityManager->beginTransaction();
        try {
            /** @var Payment $payment */
            $payment = $this->paymentRepository->find($data['paymentId']);
            $paymentGateway = $this->paymentGatewayFactory->build($payment->getMethod());
            $response = $paymentGateway->request(HttpMethod::GET, 'http://ya.ru');
            if ($response->getStatusCode() !== 200) {
                $payment->setStatus(PaymentStatus::INVOICE_STATUS_CANCELED);
            } else {
                $payment->setStatus(PaymentStatus::INVOICE_STATUS_PAID);
            }
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
            $this->entityManager->commit();

            $result->setData(['paymentStatus' => $payment->getStatus()]);
        } catch (Exception $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());
            $this->entityManager->rollback();

            return $result;
        }

        try {
            $changeInvoiceDto = new ChangeInvoiceDto();
            $changeInvoiceDto->setInvoiceId($payment->getInvoiceId());
            $this->invoiceService->changeInvoiceStatus($changeInvoiceDto);
            $changeProductStatusDto = new ChangeProductStatusDto();
            $changeProductStatusDto->setInvoiceId($payment->getInvoiceId());
            $this->productService->changeProductsStatus($changeProductStatusDto);
        } catch (Exception $exception) {
            $result->setErrorCode($exception->getCode());
            $result->setErrorMessage($exception->getMessage());

            return $result;
        }

        return $result;
    }
}