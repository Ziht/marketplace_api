<?php
declare(strict_types=1);

namespace Marketplace\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Marketplace\Dto\Service\Payment\PayInvoiceDto;
use Marketplace\Service\PaymentService;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PayInvoiceController
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * CreateInvoiceController constructor.
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        $dto = new PayInvoiceDto($request->request->all());
        $result = $this->paymentService->payInvoice($dto);
        if ($result->getErrorCode()) {
            throw new Exception($result->getErrorMessage(), $result->getErrorCode());
        }

        return new Response($result->getJsonData());
    }
}