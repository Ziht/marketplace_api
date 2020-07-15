<?php
require_once "../vendor/autoload.php";

ini_set('display_errors', 1);
error_reporting(-1);

use Core\Bootstrap\ApiBootstrap;

try {
    $bootstrap = new ApiBootstrap();
    $bootstrap->initEnv();
    $di = $bootstrap->getDi();
    $framework = $bootstrap->getFramework($di);
    $framework->run($di);
} catch (Exception $exception) {
    var_dump($exception);
}