<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\PhalconPath2PathArray;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\PathTargetAnnotationResolver;
use Phalcon\Mvc\Router\RouteInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

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
    ): RouteInterface {
        $route = $this->getMockBuilder(RouteInterface::class)->getMock();
        $route->expects(static::once())->method('getPattern')->with()->willReturn($path);
        $route->expects(static::once())->method('getHttpMethods')->with()->willReturn($methods);
        $route->expects(static::exactly($calls))->method('getReversedPaths')->with()->willReturn($config);
        return $route;
    }

    /**
     * @return array
     */
    public function provideConvert(): array
    {
        return [
            [
                '/',
                ['GET'],
                0,
                [],
                [
                    "/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                '/{var}/',
                ['GET'],
                0,
                [],
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "required" => true,
                                'schema' => [
                                    'type' => 'string',
                                    'pattern' => '.+'
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            [
                '#^/request/{id}/$#u',
                ['GET'],
                0,
                [],
                [
                    "/request/{id}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "id",
                                "in" => "path",
                                "required" => true,
                                'schema' => [
                                    'type' => 'string',
                                    'pattern' => '.+'
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            [
                '/admin/:controller/a/:action/:params/',
                ['GET'],
                0,
                [],
                [
                    "/admin/{controller}/a/{action}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "controller",
                                "in" => "path",
                                "required" => true,
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "([a-zA-Z0-9\_\-]+)"
                                ]
                            ],
                            [
                                "name" => "action",
                                "in" => "path",
                                "required" => true,
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
                '/{var:[0-9]+}/',
                ['GET'],
                0,
                [],
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "required" => true,
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
                '/([0-9a-z]+)/',
                ['GET'],
                1,
                [1 => 'var'],
                [
                    "/{var}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "required" => true,
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
                '/([0-9a-z]+)/hi/([0-9a-z]+)/',
                ['GET'],
                1,
                [1 => 'var', 2 => 'abc'],
                [
                    "/{var}/hi/{abc}/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "var",
                                "in" => "path",
                                "required" => true,
                                "schema" => [
                                    "type" => "string",
                                    "pattern" => "[0-9a-z]+"
                                ]
                            ],
                            [
                                "name" => "abc",
                                "in" => "path",
                                "required" => true,
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
                '/',
                ['GET', 'TRACE'],
                0,
                [],
                [
                    "/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "trace" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                '/any/{date:[0-9]{4}-[0-9]{2}-[0-9]{2}}/here/',
                ['GET'],
                0,
                [],
                [
                    "/any/{date}/here/" => [
                        "description" => "",
                        "get" => [
                            "description" => '',
                            "summary" => '',
                            "responses" => [
                                '200' => [
                                    "description" => 'unknown return',
                                    'content' => [
                                        '*/*' => [
                                            'schema' => new stdClass()
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "parameters" => [
                            [
                                "name" => "date",
                                "in" => "path",
                                "required" => true,
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
     * @param string $path
     * @param string[] $methods
     * @param int $calls
     * @param array $config
     * @param array $result
     * @return void
     */
    public function testConvert(
        string $path,
        array $methods,
        int $calls,
        array $config,
        array $result
    ) {
        $route = $this->makeRoute($path, $methods, $calls, $config);
        $annotations = $this->getMockBuilder(PathTargetAnnotationResolver::class)->getMock();
        $annotations->expects(static::any())
            ->method('__invoke')
            ->willReturn([
                "description" => '',
                "summary" => '',
                "responses" => []
            ]);
        static::assertEquals(
            $result,
            (new PhalconPath2PathArray(
                $annotations,
                new NoValueConversionMerger()
            )
            )->convert($route)
        );
    }
}
