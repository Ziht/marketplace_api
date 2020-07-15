<?php
use Core\Factory\EntityManagerFactory;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . "/../vendor/autoload.php";

$entityManagerFactory = new EntityManagerFactory();
try {
    (new Core\Bootstrap\ApiBootstrap())->initEnv();
    putenv('DATABASE_HOST=localhost');
    $entityManager = $entityManagerFactory->build();

    return ConsoleRunner::createHelperSet($entityManager);
} catch (Exception $e) {
    var_dump($e->getMessage());
}