<?php
declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class ExtendedTestCase
 * @package Test
 */
class ExtendedTestCase extends TestCase
{
    /**
     * @var array
     */
    protected $entityGenerators = [];
    /**
     * @param object $object
     * @param string $field
     * @param mixed $value
     * @throws ReflectionException
     */
    protected function setPrivateValue(object $object, string $field, $value): void
    {
        $reflectionClass = new ReflectionClass($object);
        var_dump($reflectionClass);
        $reflectionProperty = $reflectionClass->getProperty($field);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @param string $entityName
     * @param string $pkField
     * @param int $pk
     * @return object
     * @throws ReflectionException
     */
    protected function getEntity(string $entityName, string $pkField = 'id', $pk = 1): object
    {
        $entity = new $entityName();
        $this->setPrivateValue($entity, $pkField, $pk);
        return $entity;
    }

    /**
     * @param string $entityName
     * @param string $pkField
     * @return iterable
     * @throws ReflectionException
     */
    protected function entityGenerator(string $entityName, string $pkField = 'id'): iterable
    {
        $pk = 0;
        while (true) {
            $product = $this->getEntity($entityName, $pkField, $pk);
            $this->setPrivateValue($product, 'id', $pk++);

            yield $product;
        }

        return null;
    }

    /**
     * @param string $entityName
     * @param string $pkField
     * @return iterable
     * @throws ReflectionException
     */
    protected function getEntityGenerator(string $entityName, string $pkField = 'id'): iterable
    {
        if (!isset($this->entityGenerators[$entityName])) {
            $this->entityGenerators[$entityName] = $this->entityGenerator($entityName, $pkField);
        }

        return $this->entityGenerators[$entityName];
    }
}