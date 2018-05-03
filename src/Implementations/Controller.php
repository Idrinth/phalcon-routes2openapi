<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Controller as ControllerInterface;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2Path as P2PI;
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
    public function index(): ResponseInterface
    {
        $paths = [];
        foreach($this->router->getRoutes() as $route) {
            $paths[] = $this->di->get(P2PI::class)->convert($route);
        }
        $project = (new Composer())(dirname(__DIR__, 5).'/composer.json');
        return $this->response->setJsonContent(Merger::arrayMergeRecursiveNoConversion(
            self::$body,
            ['paths' => array_merge(...$paths), 'info' => $project]
        ));
    }

    public function indexAction(): ResponseInterface
    {
        return $this->index();
    }

    public function setRoot(string $root): ControllerInterface
    {
        $this->root = $root;
        return $this;
    }
}