#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Bootstrap\ApiBootstrap;

$bootstrap = new ApiBootstrap();
$bootstrap->initEnv();
putenv('DATABASE_HOST=localhost');