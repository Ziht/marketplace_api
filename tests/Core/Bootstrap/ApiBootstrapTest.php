<?php
declare(strict_types=1);

namespace Test\Core\Bootstrap;

use Core\Bootstrap\ApiBootstrap;
use Core\Di\DiContainer;
use Core\Di\DiContainerInterface;
use Core\Framework;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ApiBootstrapTest
 * @package Test\Core\Bootstrap
 * @coversDefaultClass \Core\Bootstrap\ApiBootstrap
 */
class ApiBootstrapTest extends TestCase
{
    /**
     * @var ApiBootstrap
     */
    protected $apiBootstrap;

    /**
     * @covers ::getDi
     * @throws Exception
     */
    public function testGetDi(): void
    {
        $di = $this->apiBootstrap->getDi();
        $this->assertNotEmpty($di);
        $this->assertInstanceOf(DiContainerInterface::class, $di);
    }

    /**
     * @covers ::getFramework
     * @throws Exception
     */
    public function testGetFramework(): void
    {
        /** @var DiContainerInterface|MockObject $di */
        $di = $this->createMock(DiContainer::class);
        $di->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValue(
                    $this->createMock(
                        Framework::class
                    )
                )
            );

        $framework = $this->apiBootstrap->getFramework($di);
        $this->assertNotEmpty($framework);
        $this->assertInstanceOf(Framework::class, $framework);
    }

    /**
     * @covers ::initEnv
     */
    public function testInitEnv(): void
    {
        try {
            $this->apiBootstrap->initEnv();
        } catch (Exception $exception) {
            $this->fail();
        }

        $this->assertTrue(TRUE);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->apiBootstrap = new ApiBootstrap();

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        unset($this->apiBootstrap);

        parent::tearDown();
    }
}