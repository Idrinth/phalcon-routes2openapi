<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

use Phalcon\Mvc\Router\RouteInterface;

/**
 * Convert Phalcon route definitions to openapi ones
 */
interface Path2PathConverter
{
  /**
   * Converts a phalcon route to a routes array for openapi
   * @param RouteInterface $route
   * @return array
   */
    public function convert(RouteInterface $route): array;
}
