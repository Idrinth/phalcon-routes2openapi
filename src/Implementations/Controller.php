<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Controller as ControllerInterface;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger as RMI;
use PackageVersions\Versions;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller as PhalconController;

/**
 * This is the controller, that handles all requests related to the index route
 */
final class Controller extends PhalconController implements ControllerInterface
{
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
        return $this
            ->getCorsEnabledResponse()
            ->setJsonContent(
                [
                    'openapi' => '3.0.1',
                    'paths' => $this->di->get(RMI::class)->mergeAll(...$paths),
                    'info' => [
                        "title" => Versions::ROOT_PACKAGE_NAME,
                        "version" => Versions::getVersion(Versions::ROOT_PACKAGE_NAME)
                    ]
                ]
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
     * @suppress PhanPluginDuplicateMethodDescription
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
     * @suppress PhanPluginDuplicateMethodDescription
     * @return-204 {"type":"string","maxLength":0}
     * @return ResponseInterface
     */
    public function optionsAction(): ResponseInterface
    {
        return $this->options();
    }

    /**
     * Return the Response with added Cross-Origin-Resource-Sharing-Headers
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
}
