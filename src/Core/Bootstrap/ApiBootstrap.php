<?php
declare(strict_types=1);

namespace Core\Bootstrap;

use Core\Di\DiContainer;
use Core\Di\DiContainerInterface;
use Core\Framework;
use Exception;
use Symfony\Component\DependencyInjection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class ApiBootstrap
 * @package Core\Bootstrap
 */
class ApiBootstrap implements BootstrapInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getFramework(DiContainerInterface $container): Framework
    {
        /** @var Framework $framework */
        $framework = $container->get('Core\Framework');

        return $framework;
    }

    /**
     * @throws Exception
     */
    public function getDi(): DiContainerInterface
    {
        $confDir = __DIR__ . '/../../../config/';
        $environment = $this->getEnvironment();
        $fileLocator = new FileLocator($confDir);
        $container = new DiContainer();
        $loader = new DependencyInjection\Loader\YamlFileLoader($container, $fileLocator);
        $loader->load('packages/framework.yaml', 'glob');
        if ($environment === 'test') {
            $loader->load('packages/test.yaml', 'glob');
        } elseif ($environment === 'dev') {
            $loader->load('packages/services.yaml', 'glob');
        }
        if (is_dir($confDir . 'packages/' . $environment)) {
            $loader->load('packages/' . $environment . '/framework.yaml', 'glob');
            $loader->load('packages/' . $environment . '/services.yaml', 'glob');
        }

        return $container;
    }

    public function initEnv(): void
    {
        $dotenv = new Dotenv();
        $dotenv->usePutenv(true);
        $dotenv->bootEnv(__DIR__ . '/../../../.env');
    }

    protected function getEnvironment()
    {
        return getenv('APP_ENV');
    }
}
