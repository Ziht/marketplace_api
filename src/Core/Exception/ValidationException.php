<?php
declare(strict_types=1);

namespace Core\Exception;

use Exception;

/**
 * Class ValidationException
 * @package Core\Exception
 */
class ValidationException extends Exception
{
    /**
     * @var int
     */
    protected $code = 1000;
}