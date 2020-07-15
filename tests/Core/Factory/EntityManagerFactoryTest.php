<?php
declare(strict_types=1);

namespace Test\Core\Factory;

use Core\Factory\EntityManagerFactory;
use Doctrine\ORM\EntityManager;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class EntityManagerFactoryTest
 * @package Test\Core\Factory
 * @coversDefaultClass \Core\Factory\EntityManagerFactory
 */
class EntityManagerFactoryTest extends TestCase
{
    protected $entityManagerFactory;

    /**
     * @covers ::build
     * @throws Exception
     */
    public function testBuild(): void
    {
        $entityManager = $this->entityManagerFactory->build();
        $this->assertNotEmpty($entityManager);
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    protected function setUp(): void
    {
        $this->entityManagerFactory = new EntityManagerFactory();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->entityManagerFactory);

        parent::tearDown();
    }
}