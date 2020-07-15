<?php
declare(strict_types=1);

namespace Marketplace\Service;

use Core\Exception\ValidationException;
use Core\HttpClient\HttpMethod;
use Core\PaymentGateway\FakePaymentGateway;
use Core\Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
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
     * @var FakePaymentGateway
     */
    protected $fakePaymentGateway;

    /**
     * @var PaymentBll
     */
    protected $paymentBll;

    public function __construct(
        EntityManager $entityManager,
        InvoiceRepository $invoiceRepository,
        PaymentRepository $paymentRepository,
        UserService $userService,
        InvoiceService $invoiceService,
        ProductService $productService,
        FakePaymentGateway $fakePaymentGateway,
        PaymentBll $paymentBll
    ) {
        $this->entityManager = $entityManager;
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;

        $this->userService = $userService;
        $this->invoiceService = $invoiceService;
        $this->productService = $productService;

        $this->fakePaymentGateway = $fakePaymentGateway;
        $this->paymentBll = $paymentBll;
    }

    /**
     * @param PayInvoiceDto $payInvoiceDto
     * @return Result
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     * @throws GuzzleException
     */
    public function payInvoice(PayInvoiceDto $payInvoiceDto): Result
    {
        $result = new Result();
        $this->entityManager->beginTransaction();
        try {
            if ($payInvoiceDto->isNeedValidate()) {
                $this->validatePayInvoiceDto($payInvoiceDto);
            }
            $response = $this->fakePaymentGateway->request(HttpMethod::GET, 'http://ya.ru');

            $payment = new Payment();
            $payment->setInvoiceId($payInvoiceDto->getInvoiceId());
            $payment->setMethod($payInvoiceDto->getPaymentMethod());
            $payment->setAmount($payInvoiceDto->getPaymentAmount());
            $payment->setStatus(PaymentStatus::INVOICE_STATUS_SENT);

            if ($response->getStatusCode() !== 200) {
                $payment->setStatus(PaymentStatus::INVOICE_STATUS_CANCELED);
            } else {
                $payment->setStatus(PaymentStatus::INVOICE_STATUS_PAID);
            }
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
            $this->entityManager->commit();

            $result->setData(['paymentStatus' => $payment->getStatus()]);
        } catch (ValidationException $exception) {
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
            throw new ValidationException('Invoice ID not set.');
        }
        if (!$dto->getPaymentAmount()) {
            throw new ValidationException('Payment amount not established.');
        }
        if (!$dto->getPaymentMethod() || !in_array($dto->getPaymentMethod(), PaymentMethod::$values)) {
            throw new ValidationException('Incorrect payment method.');
        }
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->find($dto->getInvoiceId());
        if (in_array($invoice->getStatus(), InvoiceStatus::$completed)) {
            throw new ValidationException('Invoice is sold or cancelled.');
        }
        $payments = $this->paymentRepository->findByInvoiceId($dto->getInvoiceId());
        $remainingAmount = $this->paymentBll->calcRemainingAmount($invoice, $payments);
        if ($dto->getPaymentAmount() > $remainingAmount) {
            throw new ValidationException('Amount exceeds remaining payment amount. You need to pay ' . $remainingAmount . '.');
        }
    }
}