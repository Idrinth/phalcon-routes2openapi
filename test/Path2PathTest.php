<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\PhalconPath2PathArray;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\PathTargetAnnotationResolver;
use Phalcon\Mvc\Router\RouteInterface;
use PHPUnit\Framework\TestCase;

class Path2PathTest extends TestCase
{
    /**
     * @param string $path
     * @param array $methods
     * @param int $calls
     * @param array $config
     * @return RouteInterface
     */
    private function makeRoute(
        string $path,
        array $methods,
        int $calls = 0,
        array $config = array()
    ):RouteInterface {
        $route = $this->getMockBuilder(RouteInterface::class)->getMock();
        $route->expects($this->once())->method('getPattern')->with()->willReturn($path);
        $route->expects($this->once())->method('getHttpMethods')->with()->willReturn($methods);
        $route->expects($this->exactly($calls))->method('getReversedPaths')->with()->willReturn($config);
        return $route;
    }

    /**
     * @return array
     */
    public function provideConvert(): array
    {
        return [
            [
                $this->makeRoute('/', ['GET']),
                [
                    "/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/{var}/', ['GET']),
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path"
                            ]
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('#^/request/{id}/$#u', ['GET']),
                [
                    "/request/{id}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "id",
                                "in" => "path"
                            ]
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/admin/:controller/a/:action/:params/', ['GET']),
                [
                    "/admin/{controller}/a/{action}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "controller",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "([a-zA-Z0-9\_\-]+)"
                                ]
                            ],
                            [
                                "name" => "action",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "([a-zA-Z0-9\_\-]+)"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/{var:[0-9]+}/', ['GET']),
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "[0-9]+"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/([0-9a-z]+)/', ['GET'], 1, [1 => 'var']),
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "[0-9a-z]+"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/([0-9a-z]+)/hi/([0-9a-z]+)/', ['GET'], 1, [1 => 'var', 2 => 'abc']),
                [
                    "/{var}/hi/{abc}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "[0-9a-z]+"
                                ]
                            ],
                            [
                                "name" => "abc",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "[0-9a-z]+"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/', ['GET', 'TRACE']),
                [
                    "/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "trace" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ]
                    ]
                ]
            ],
            [
                $this->makeRoute('/any/{date:[0-9]{4}-[0-9]{2}-[0-9]{2}}/here/', ['GET']),
                [
                    "/any/{date}/here/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => []
                        ],
                        "parameters" => [
                            [
                                "name" => "date",
                                "in" => "path",
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "[0-9]{4}-[0-9]{2}-[0-9]{2}"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideConvert
     * @param RouteInterface $route
     * @param array $result
     */
    public function testConvert(RouteInterface $route, array $result)
    {
        $annotations = $this->getMockBuilder(PathTargetAnnotationResolver::class)->getMock();
        $annotations->expects($this->any())
            ->method('__invoke')
            ->willReturn([
                "description" => '',
                "summary" => '',
                "responses" => []
            ]);
        $this->assertEquals(
            $result,
            (new PhalconPath2PathArray(
                $annotations,
                new NoValueConversionMerger()
            )
            )->convert($route)
        );
    }
}
