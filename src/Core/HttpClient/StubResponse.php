<?php
declare(strict_types=1);

namespace Core\HttpClient;

class StubResponse implements ResponseInterface
{

    public function getStatusCode()
    {
        return 200;
    }
}