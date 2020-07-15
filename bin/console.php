#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Command\CreateProductsCommand;
use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->in(__DIR__ . '/../src/Command/');
$application = new Application();
(new Core\Bootstrap\ApiBootstrap())->initEnv();
putenv('DATABASE_HOST=localhost');
$application->addCommands([
    new CreateProductsCommand()
]);

try {
    $application->run();
} catch (Exception $e) {
}