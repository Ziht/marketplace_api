<?php
declare(strict_types=1);

namespace Core\Dto;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Dto
{
    /**
     * @var bool
     */
    protected $isNeedValidate;

    /**
     * Dto constructor.
     * @param array $parameters
     * @param bool $isNeedValidate
     * @throws ReflectionException
     */
    public function __construct(array $parameters = [], bool $isNeedValidate = true)
    {
        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            if (isset($parameters[$property])) {
                $this->{$property} = $parameters[$property];
            }
        }
        $this->isNeedValidate = $isNeedValidate;
    }

    /**
     * @return bool
     */
    public function isNeedValidate(): bool
    {
        return $this->isNeedValidate;
    }
}