<?php
declare(strict_types=1);

namespace Core\PaymentGateway;

use Core\HttpClient\ResponseInterface;

interface PaymentGatewayInterface
{
    /**
     * @param string $requestMethod
     * @param string $uri
     * @return ResponseInterface
     */
    public function request(string $requestMethod, string $uri): ResponseInterface;
}