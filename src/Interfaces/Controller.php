<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

use Phalcon\Http\ResponseInterface;

/**
 * Probvides the endpoints for the documentation route, / by default
 */
interface Controller
{
    /**
     * generates api-documentation
     * @return ResponseInterface
     * @return-200 application/json {"type":"object"}
     */
    public function index(): ResponseInterface;

    /**
     * generates api-documentation
     * @return ResponseInterface
     * @return-200 application/json {"type":"object"}
     * @suppress PhanPluginDuplicateMethodDescription
     */
    public function indexAction(): ResponseInterface;

    /**
     * Generates an overview over routes registered
     * @return-204 {"type":"string","maxLength":0}
     * @return ResponseInterface
     */
    public function options(): ResponseInterface;

    /**
     * Generates an overview over routes registered
     * @return-204 {"type":"string","maxLength":0}
     * @return ResponseInterface
     * @suppress PhanPluginDuplicateMethodDescription
     */
    public function optionsAction(): ResponseInterface;
}
