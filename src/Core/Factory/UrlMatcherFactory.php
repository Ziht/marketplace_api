<?php
declare(strict_types=1);

namespace Core\Factory;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

/**
 * Class UrlMatcherFactory
 * @package Core\Factory
 */
class UrlMatcherFactory
{

    public function build(RequestContext $context)
    {
        $fileLocator = new FileLocator(__DIR__ . '/../../../config/routes/');
        $loader = new YamlFileLoader($fileLocator);
        $routes = $loader->load('routes.yaml');

        return new UrlMatcher($routes, $context);
    }
}