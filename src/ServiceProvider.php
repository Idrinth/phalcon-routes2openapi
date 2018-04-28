<?php

namespace De\Idrinth\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Controller as ControllerImplementation;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Controller;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Path2Path as Path2PathImplementation;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2Path;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class ServiceProvider implements ServiceProviderInterface
{
    private $apiRoot;
    public function __construct(string $apiRoot = '/')
    {
        $this->apiRoot = $apiRoot;
    }

    public function register(DiInterface $di)
    {
        $root = $this->apiRoot;
        $di->set(Controller::class, function() use ($root) {
            return (new ControllerImplementation())->setRoot($root);
        });
        $di->set(Path2Path::class, function() {
            return (new Path2PathImplementation());
        });
        $di->get('router')->addGet($root, ['controller' => Controller::class, 'action' => 'index']);
    }
}