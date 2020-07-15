<?php
declare(strict_types=1);

namespace Marketplace\Controller;

use Exception;
use Marketplace\Dto\Service\Invoice\CreateInvoiceDto;
use Marketplace\Service\InvoiceService;
use Marketplace\Service\UserService;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateInvoiceController
 * @package Marketplace\Controller
 */
class CreateInvoiceController
{
    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * CreateInvoiceController constructor.
     * @param InvoiceService $invoiceService
     * @param UserService $userService
     */
    public function __construct(InvoiceService $invoiceService, UserService $userService)
    {
        $this->invoiceService = $invoiceService;
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        $dto = new CreateInvoiceDto($request->request->all());
        $result = $this->invoiceService->createInvoice($dto);
        if ($result->getErrorCode()) {
            throw new Exception($result->getErrorMessage(), $result->getErrorCode());
        }

        return new Response($result->getJsonData());
    }
}