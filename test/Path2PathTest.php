<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Path2Path;
use Phalcon\Mvc\Router\RouteInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

class Path2PathTest extends TestCase
{
    private function makeRoute(string $path, array $methods, bool $hasUnnamed = false, array $config = array()):RouteInterface
    {
        $route = $this->getMockBuilder(RouteInterface::class)->getMock();
        $route->expects($this->once())->method('getPattern')->with()->willReturn($path);
        $route->expects($this->once())->method('getHttpMethods')->with()->willReturn($methods);
        $route->expects($this->exactly($hasUnnamed?1:0))->method('getReversedPaths')->with()->willReturn($config);
        return $route;
    }
    public function provideConvert()
    {
        return [
            [
                $this->makeRoute('/', ['GET']),
                [
                    "/" => [
                        "description" => "",
                        "get" => [
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                $this->makeRoute('/([0-9a-z]+)/', ['GET'], true, [1 => 'var']),
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                $this->makeRoute('/([0-9a-z]+)/hi/([0-9a-z]+)/', ['GET'], true, [1 => 'var', 2 => 'abc']),
                [
                    "/{var}/hi/{abc}/" => [
                        "description" => "",
                        "get" => [
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
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
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
                                ]
                            ]
                        ],
                        "trace" => [
                            "responses" => [
                                "200" => [
                                    "description" => "",
                                    "content" => [
                                        "application/json" => new stdClass(),
                                    ]
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
        $this->assertEquals($result, (new Path2Path())->convert($route));
    }
}
