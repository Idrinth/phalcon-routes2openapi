<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Controller as ControllerInterface;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller as PhalconController;

class Controller extends PhalconController implements ControllerInterface
{
    /**
     * @var string
     */
    private $root;

    /**
     * @var array
     */
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
        $converter = $this->di->get(Path2PathConverter::class);
        foreach ($this->router->getRoutes() as $route) {
            $paths[] = $converter->convert($route);
        }
        $merger = $this->di->get(RecursiveMerger::class);
        return $this
            ->getCorsEnabledResponse()
            ->setJsonContent(
                $merger->merge(
                    self::$body,
                    [
                        'paths' => $merger->mergeAll(...$paths),
                        'info' => (new Composer())(dirname(__DIR__, 5).'/composer.json')
                    ]
                )
            );
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
     * Generates an overview over routes registered
     * @return-204 {"type":"string","maxLength":0}
     * @return ResponseInterface
     */
    public function options(): ResponseInterface
    {
        return $this
            ->getCorsEnabledResponse()
            ->setStatusCode(204);
    }

    /**
     * Generates an overview over routes registered
     * @return-204 {"type":"string","maxLength":0}
     * @return ResponseInterface
     */
    public function optionsAction(): ResponseInterface
    {
        return $this->options();
    }

    /**
     * @return ResponseInterface
     */
    private function getCorsEnabledResponse(): ResponseInterface
    {
        return $this->response
            ->setHeader(
                'Access-Control-Allow-Origin',
                $this->request->getHeader('Origin') ?: '*'
            )
            ->setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
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
