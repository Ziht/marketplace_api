<?php
declare(strict_types=1);

namespace Core;

use Core\Di\DiContainerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Framework
 * @package Core
 */
class Framework extends HttpKernel
{
    public function __construct(
        EventDispatcherInterface $dispatcher,
        ControllerResolverInterface $resolver,
        RequestStack $requestStack = null,
        ArgumentResolverInterface $argumentResolver = null
    ) {

        parent::__construct($dispatcher, $resolver, $requestStack, $argumentResolver);
    }

    /**
     * @param DiContainerInterface $container
     * @throws Exception
     */
    public function run(DiContainerInterface $container): void
    {
        $container->compile();
        $request = $this->getRequest();
        try {
            $response = $this->handle($request);
        } catch (Exception $exception) {
            $response = new Response(json_encode([
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage(),
                'data' => '',
            ]));
        }
        $response->send();
        $this->terminate($request, $response);
    }

    protected function getRequest()
    {
        $request = Request::createFromGlobals();
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        return $request;
    }
}