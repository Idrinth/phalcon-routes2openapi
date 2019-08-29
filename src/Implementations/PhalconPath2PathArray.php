<?php declare(strict_types=1);

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
    private function getBasicPath(RouteInterface $route): string
    {
        $result = str_replace(
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
            ) ?? ''
        );
        return is_string($result) ? $result : '';
    }

    /**
     * @param string $path
     * @param array $openapi
     * @return void
     */
    private function handleParams(string &$path, array &$openapi)
    {
        if ((int) preg_match_all('/(\{([^{}]+?|\\\\\\{|\\\\\\}|(?R))+\\})/', $path, $matches) > 0) {
            foreach ($matches[1] as $match) {
                $parts = explode(':', substr($match, 1, -1), 2);
                $param = [
                    'in' => 'path',
                    'name' => $parts[0],
                    'required' => true,
                    'schema' => [
                        'type' => 'string',
                        'pattern' => count($parts) === 2 ? $parts[1] : '.+'
                    ]
                ];
                if (count($parts) === 2) {
                    $path = str_replace($match, '{'.$parts[0].'}', $path);
                }
                $openapi['parameters'][] = $param;
            }
        }
    }

    /**
     * @param string $path
     * @param array $openapi
     * @param RouteInterface $route
     * @return void
     */
    private function handleQuery(string &$path, array &$openapi, RouteInterface $route)
    {
        if ((int) preg_match_all('/\((.+?)\)/', $path, $matches) > 0) {
            $names = $route->getReversedPaths();
            foreach ($matches[1] as $pos => $match) {
                $name = $names[$pos+1];
                $openapi['parameters'][] = [
                    'name' => $name,
                    'in' => 'path',
                    'required' => true,
                    'schema' => [
                        'type' => 'string',
                        'pattern' => $match
                    ]
                ];
                $path = preg_replace('/'.preg_quote('('.$match.')', '/').'/', '{'.$name.'}', $path, 1);
            }
        }
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
        $this->handleParams($path, $openapi);
        $this->handleQuery($path, $openapi, $route);
        $data = $this->pathTargetReflector->__invoke(
            (string) ($route->getPaths()['controller'] ?? ''),
            (string) ($route->getPaths()['action'] ?? '')
        );
        $methods = ['get'];
        foreach ((array)$route->getHttpMethods() as $method) {
            $methods[] = strtolower($method);
            $openapi[strtolower($method)] = $this->merger->merge((array) ($openapi[strtolower($method)]??[]), $data);
        }
        foreach (array_unique($methods) as $method) {
            $openapi[$method] = DefaultResponse::add($openapi[$method]??[]);
        }
        ksort($openapi);
        return [$path => $openapi];
    }
}
