<?php
declare(strict_types=1);

namespace Core\Bootstrap;

use Core\Di\DiContainerInterface;
use Core\Framework;

interface BootstrapInterface
{
    /**
     * @param DiContainerInterface $containerBuilder
     * @return Framework
     */
    public function getFramework(DiContainerInterface $containerBuilder): Framework;

    /**
     * @return DiContainerInterface
     */
    public function getDi(): DiContainerInterface;

    /**
     * @return void
     */
    public function initEnv(): void;
}