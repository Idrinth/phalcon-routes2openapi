<?php

declare(strict_types=1);

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
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\RouterInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;

/**
 * Registers the controller, the services and the routes
 * @suppress PhanUnreferencedClass
 */
final class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers controller at api-root
     * @param DiInterface $serviceContainer
     * @return void
     */
    public function register(DiInterface $serviceContainer)
    {
        $this->registerServices($serviceContainer);
        $this->registerRoutes($serviceContainer->get('router'));
    }

    /**
     * Register Services
     * @param DiInterface $serviceContainer
     * @return void
     */
    private function registerServices(DiInterface $serviceContainer)
    {
        $serviceContainer->set(Controller::class, ControllerImplementation::class);
        $serviceContainer->set(
            Path2PathConverter::class,
            /**
             * @return Path2PathConverter
             */
            function () use (&$serviceContainer): Path2PathConverter {
                return new PhalconPath2PathArray(
                    $serviceContainer->get(PathTargetAnnotationResolver::class),
                    $serviceContainer->get(RecursiveMerger::class)
                );
            }
        );
        $serviceContainer->set(
            DocBlockFactoryInterface::class,
            /**
             * @return DocBlockFactory
             */
            function (): DocBlockFactoryInterface {
                return DocBlockFactory::createInstance();
            }
        );
        $serviceContainer->set(
            PathTargetAnnotationResolver::class,
            /**
             * Creates a Reflector
             * @return PathTargetAnnotationResolver
             */
            function () use (&$serviceContainer): PathTargetAnnotationResolver {
                return new Reflector(
                    $serviceContainer->get(DocBlockFactoryInterface::class),
                    $serviceContainer->get(RecursiveMerger::class)
                );
            }
        );
        $serviceContainer->set(RecursiveMerger::class, NoValueConversionMerger::class);
    }

    /**
     * Register routes
     * @param RouterInterface $router
     * @return void
     */
    private function registerRoutes(RouterInterface $router)
    {
        $router->addGet('/', ['controller' => Controller::class, 'action' => 'index']);
        $router->addOptions('/', ['controller' => Controller::class, 'action' => 'options']);
    }
}
