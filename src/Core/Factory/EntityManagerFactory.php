<?php
declare(strict_types=1);

namespace Core\Factory;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

/**
 * Class EntityManagerFactory
 * @package Core\Factory
 */
class EntityManagerFactory
{

    /**
     * @return EntityManager
     * @throws ORMException
     */
    public function build()
    {
        $cache = new ArrayCache();
        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver(__DIR__ . '/../../', false);
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(__DIR__ . '/../../../cache/doctrine/proxy');
        $config->setProxyNamespace('Marketplace\Entity');
        $config->setAutoGenerateProxyClasses(true);

        $connectionOptions = [
            'dbname' => getenv('DATABASE_NAME'),
            'user' => getenv('DATABASE_USER'),
            'password' => getenv('DATABASE_PWD'),
            'host' => getenv('DATABASE_HOST'),
            'port' => getenv('DATABASE_PORT'),
            'driver' => 'pdo_mysql',
        ];

        return EntityManager::create($connectionOptions, $config);
    }
}