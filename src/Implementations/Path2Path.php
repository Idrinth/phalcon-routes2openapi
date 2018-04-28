<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use Phalcon\Mvc\Router\RouteInterface;

class Path2Path implements \De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2Path
{
    public function convert(RouteInterface $route):array
    {
        $openapi = [
            "description" => ""
        ];
        $path = str_replace(
            [':controller',':action',':module',':namespace',':int'],
            [
                '{controller:([a-zA-Z0-9\_\-]+)}',
                '{action:([a-zA-Z0-9\_\-]+)}',
                '{module:([a-zA-Z0-9\_\-]+)}',
                '{namespace:([a-zA-Z0-9\_\-]+)}',
                '([0-9]+)'
            ],
            preg_replace('/:params\/?$/', '', $route->getCompiledPattern())
        );
        if (preg_match_all('/\{(.+?)\}/', $path, $matches)) {
            foreach ($matches[1] as $match) {
                $parts = explode(':', $match, 2);
                $param = [
                    'in' => 'path',
                    'name' => $parts[0]
                ];
                if(count($parts) === 2) {
                    $param['schema'] = [
                        'type' => 'string',
                        'pattern' => $parts[1]
                    ];
                    $path = str_replace('{'.$match.'}', '{'.$parts[0].'}', $path);
                }
                $openapi['parameters'][] = $param;
            }
        }
        if (preg_match_all('/\((.+?)\)/', $path, $matches)) {
            $names = $route->getReversedPaths();
            foreach ($matches[1] as $pos => $match) {
                $name = $names[$pos+1];
                $openapi['parameters'][] = [
                    'in' => 'path',
                    'name' => $name,
                    'schema' => [
                        'type' => 'string',
                        'pattern' => $match
                    ]
                ];
                $path = preg_replace('/'.preg_quote('('.$match.')', '/').'/', '{'.$name.'}', $path, 1);
            }
        }
        foreach ((array)$route->getHttpMethods() as $method) {
            $openapi[strtolower($method)] = [
                "responses" => [
                    "200" => [
                        "description" => "",
                        "application/json" => new \stdClass()
                    ]
                ]
            ];
        }
        ksort($openapi);
        return [$path => $openapi];
    }
}