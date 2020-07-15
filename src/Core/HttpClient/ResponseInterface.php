<?php
declare(strict_types=1);

namespace Core\HttpClient;

interface ResponseInterface
{
    public function getStatusCode();
}