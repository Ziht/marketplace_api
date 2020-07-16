#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(-1);

use Core\Bootstrap\ApiBootstrap;
use Core\Mq\Rabbitmq;

try {
    $bootstrap = new ApiBootstrap();
    $bootstrap->initEnv();
    putenv('DATABASE_HOST=localhost');
    $di = $bootstrap->getDi();
    $di->compile();
    $rabbitmq = new Rabbitmq($di);
    $rabbitmq->receive('defaultQueue');
} catch (Exception $exception) {
    var_dump($exception);
}