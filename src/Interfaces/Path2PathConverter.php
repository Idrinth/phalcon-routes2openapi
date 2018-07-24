<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

use Phalcon\Mvc\Router\RouteInterface;

interface Path2PathConverter
{
    public function convert(RouteInterface $route):array;
}
