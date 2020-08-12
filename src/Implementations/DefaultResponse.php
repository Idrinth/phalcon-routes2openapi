<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use stdClass;

class DefaultResponse
{
    /**
     * @param array $route
     * @return array
     */
    public static function add(array $route): array
    {
        if (!isset($route['responses']) || count($route['responses']) === 0) {
            $route['responses'] = [
                '200' => [
                    "description" => 'unknown return',
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
