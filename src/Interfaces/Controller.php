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
     */
    public function index():ResponseInterface;

    /**
     * generates api-documentation
     * @return ResponseInterface
     */
    public function indexAction():ResponseInterface;
}