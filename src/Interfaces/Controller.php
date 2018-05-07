<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

use Phalcon\Http\ResponseInterface;

interface Controller
{
    /**
     * Sets the api's root, used for filtering
     * @param string $root
     */
    public function setRoot(string $root):Controller;

    /**
     * generates api-documentation
     * @return ResponseInterface
     * @return-200 application/json {"type":"object"}
     */
    public function index():ResponseInterface;

    /**
     * generates api-documentation
     * @return ResponseInterface
     * @return-200 application/json {"type":"object"}
     */
    public function indexAction():ResponseInterface;
}
