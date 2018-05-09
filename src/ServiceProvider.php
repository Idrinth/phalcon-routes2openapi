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
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    private $apiRoot;

    /**
     * @param string $apiRoot
     */
    public function __construct(string $apiRoot = '/')
    {
        $this->apiRoot = ($apiRoot{0}==='/'?'':'/').$apiRoot;
    }

    /**
     * Registers controller at api-root
     * @param DiInterface $di
     * @return void
     */
    public function register(DiInterface $di)
    {
        $root = $this->apiRoot;
        $di->set(Controller::class, function() use ($root) {
            return (new ControllerImplementation())->setRoot($root);
        });
        $di->set(Path2PathConverter::class, function() {
            return new PhalconPath2PathArray(
                $this->get(PathTargetAnnotationResolver::class),
                $this->get(Merger::class)
            );
        });
        $di->set(DocBlockFactoryInterface::class, function() {
            return DocBlockFactory::createInstance();
        });
        $di->set(PathTargetAnnotationResolver::class, function() {
            return new Reflector($this->get(DocBlockFactoryInterface::class), $this->get(Merger::class));
        });
        $di->set(RecursiveMerger::class, NoValueConversionMerger::class);
        $di->set(Merger::class, NoValueConversionMerger::class);
        $di->get('router')->addGet($root, ['controller' => Controller::class, 'action' => 'index']);
    }
}
