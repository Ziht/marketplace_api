<?php
declare(strict_types=1);

namespace Core\PaymentGateway;

use Core\HttpClient\GuzzleHttpClient;
use Core\HttpClient\GuzzleResponse;
use Core\HttpClient\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

class FakePaymentGateway extends PaymentGateway implements PaymentGatewayInterface
{
    /**
     * @inheritDoc
     * @throws GuzzleException
     */
    public function request(string $requestMethod, string $uri): ResponseInterface
    {
        /** @var $httpClient GuzzleHttpClient */
        $httpClient = $this->httpClient;
        $response = $httpClient->request($requestMethod, $uri, $this->options);
        return new GuzzleResponse(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }
}