<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\PathTargetAnnotationResolver;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use Phalcon\Mvc\Router\RouteInterface;

class PhalconPath2PathArray implements Path2PathConverter
{
    /**
     * @var PathTargetAnnotationResolver
     */
    private $pathTargetReflector;

    /**
     * @var RecursiveMerger
     */
    private $merger;

    /**
     * @param PathTargetAnnotationResolver $pathTargetReflector
     * @param RecursiveMerger $merger
     */
    public function __construct(PathTargetAnnotationResolver $pathTargetReflector, RecursiveMerger $merger)
    {
        $this->pathTargetReflector = $pathTargetReflector;
        $this->merger = $merger;
    }

    /**
     * @param RouteInterface $route
     * @return string
     */
    private function getBasicPath(RouteInterface $route):string
    {
        return str_replace(
            [':controller',':action',':module',':namespace',':int'],
            [
                '{controller:([a-zA-Z0-9\_\-]+)}',
                '{action:([a-zA-Z0-9\_\-]+)}',
                '{module:([a-zA-Z0-9\_\-]+)}',
                '{namespace:([a-zA-Z0-9\_\-]+)}',
                '([0-9]+)'
            ],
            preg_replace(
                '/:params:?\/?$/',
                '',
                preg_replace('/^#\^(.*)\$#.*?$/', '$1', $route->getPattern())
            )
        );
    }

    /**
     * @param RouteInterface $route
     * @return array
     */
    public function convert(RouteInterface $route):array
    {
        $openapi = [
            "description" => ""
        ];
        $path = $this->getBasicPath($route);
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
                    'name' => $name,
                    'in' => 'path',
                    'schema' => [
                        'type' => 'string',
                        'pattern' => $match
                    ]
                ];
                $path = preg_replace('/'.preg_quote('('.$match.')', '/').'/', '{'.$name.'}', $path, 1);
            }
        }
        $data = $this->pathTargetReflector->__invoke(
            (string) $route->getPaths()['controller'],
            (string) $route->getPaths()['action']
        );
        foreach ((array)$route->getHttpMethods() as $method) {
            $openapi[strtolower($method)] = $this->merger->merge($openapi[strtolower($method)]??[], $data);
        }
        ksort($openapi);
        return [$path => $openapi];
    }
}
