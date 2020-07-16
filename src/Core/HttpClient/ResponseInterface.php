<?php
declare(strict_types=1);

namespace Core\HttpClient;

/**
 * Interface ResponseInterface
 * @package Core\HttpClient
 */
interface ResponseInterface
{
    public function getStatusCode();
}