<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use stdClass;

/**
 * Provides a default route for not correctly defined methods
 */
final class DefaultResponse
{
    /**
     * Add a defaul response of 200 to paths without a defined one
     * @param string[]|array[] $route
     * @return string[]|array[]
     */
    public static function add(array $route): array
    {
        if (!isset($route['responses']) || count($route['responses']) === 0) {
            $route['responses'] = [
                '200' => [
                    'description' => 'unknown return',
                    'content' => [
                        '*/*' => [
                            'schema' => new stdClass()
                        ]
                    ]
                ]
            ];
        }
        $route['summary'] = $route['summary'] ?? '';
        $route['description'] = $route['description'] ?? '';
        return $route;
    }
}
