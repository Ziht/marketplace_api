<?php
declare(strict_types=1);

namespace Core\PaymentGateway;

use Core\HttpClient\HttpClientInterface;
use Core\HttpClient\ResponseInterface;
use Core\HttpClient\StubResponse;

/**
 * Class PaymentGateway
 * @package Core\PaymentGateway
 */
class PaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * PaymentGateway constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->setHttpClient($httpClient);
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $requestMethod
     * @param string $uri
     * @return ResponseInterface
     */
    public function request(string $requestMethod, string $uri): ResponseInterface
    {
        return new StubResponse();
    }
}