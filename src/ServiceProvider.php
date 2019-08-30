<?php

namespace De\Idrinth\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Controller as ControllerImplementation;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\PhalconPath2PathArray;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Reflector;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Controller;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\PathTargetAnnotationResolver;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\RouterInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param string $apiRoot
     */
    public function __construct(string $apiRoot = '/')
    {
        $this->apiRoot = ($apiRoot{0}==='/' ? '' : '/') . $apiRoot;
    }

    /**
     * Registers controller at api-root
     * @param DiInterface $serviceContainer
     * @return void
     */
    public function register(DiInterface $serviceContainer)
    {
        $this->registerServices($serviceContainer, $this->apiRoot);
        $this->registerRoutes($serviceContainer->get('router'));
    }

    /**
     * @param DiInterface $serviceContainer
     * @param string $root
     * @return void
     */
    private function registerServices(DiInterface $serviceContainer, string $root)
    {
        $serviceContainer->set(Controller::class, function () use ($root) {
            return (new ControllerImplementation())->setRoot($root);
        });
        $serviceContainer->set(Path2PathConverter::class, function () use (&$serviceContainer) {
            return new PhalconPath2PathArray(
                $serviceContainer->get(PathTargetAnnotationResolver::class),
                $serviceContainer->get(RecursiveMerger::class)
            );
        });
        $serviceContainer->set(DocBlockFactoryInterface::class, function () {
            return DocBlockFactory::createInstance();
        });
        $serviceContainer->set(PathTargetAnnotationResolver::class, function () use (&$serviceContainer) {
            return new Reflector(
                $serviceContainer->get(DocBlockFactoryInterface::class),
                $serviceContainer->get(RecursiveMerger::class)
            );
        });
        $serviceContainer->set(RecursiveMerger::class, NoValueConversionMerger::class);
    }

    /**
     * @param RouterInterface $router
     * @return void
     */
    private function registerRoutes(RouterInterface $router)
    {
        $router->addGet('/', ['controller' => Controller::class, 'action' => 'index']);
        $router->addOptions('/', ['controller' => Controller::class, 'action' => 'options']);
    }
}
