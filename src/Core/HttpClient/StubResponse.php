<?php
declare(strict_types=1);

namespace Core\HttpClient;

/**
 * Class StubResponse
 * @package Core\HttpClient
 */
class StubResponse implements ResponseInterface
{
    /**
     * @return int
     */
    public function getStatusCode()
    {
        return 200;
    }
}