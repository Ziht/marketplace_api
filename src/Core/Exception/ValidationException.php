<?php
declare(strict_types=1);

namespace Core\Exception;

use Exception;

class ValidationException extends Exception
{
    protected $code = 1000;
}