<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Controller as ControllerInterface;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller as PhalconController;

class Controller extends PhalconController implements ControllerInterface
{
    private $root;
    private static $body = [
        "openapi"=> "3.0.1",
        "info"=> [
            "title"=> "unknown",
            "version"=> "1.0.0"
        ]
    ];

    /**
     * Generates an overview over routes registered
     * @return-200 application/json {"type":"object"}
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $paths = [];
        foreach ($this->router->getRoutes() as $route) {
            $paths[] = $this->di->get(Path2PathConverter::class)->convert($route);
        }
        return $this->response->setJsonContent($this->di->get(RecursiveMerger::class)->merge(
            self::$body,
            ['paths' => array_merge(...$paths), 'info' => (new Composer())(dirname(__DIR__, 5).'/composer.json')]
        ));
    }

    /**
     * Generates an overview over routes registered
     * @return-200 application/json {"type":"object"}
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        return $this->index();
    }

    /**
     * @param string $root
     * @return ControllerInterface
     */
    public function setRoot(string $root): ControllerInterface
    {
        $this->root = $root;
        return $this;
    }
}
