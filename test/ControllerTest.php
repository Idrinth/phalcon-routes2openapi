<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Controller;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use Phalcon\DiInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Mvc\RouterInterface;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    /**
     * @return RouterInterface
     */
    private function buildRouterMock(): RouterInterface
    {
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $router->expects(static::once())
            ->method('getRoutes')
            ->with()
            ->willReturn([$this->getMockBuilder(RouteInterface::class)->getMock()]);
        return $router;
    }

    /**
     * @return array
     */
    public function provideIndex(): array
    {
        return [
            [
                $this->buildRouterMock(),
                '/',
                [
                    "openapi" => "3.0.1",
                    "info" => [
                        "title" => "unknown",
                        "version" => "1.0.0"
                    ],
                    "paths" => [
                        '/abc' => [
                            'get' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param RouterInterface $router
     * @param string $root
     * @param array $result
     * @return Controller
     */
    private function getPreparedInstance(RouterInterface $router, string $root, array $result): Controller
    {
        $instance           = new Controller();
        $instance->router   = $router;
        $instance->setRoot($root);
        $di                 = $this->getMockBuilder(DiInterface::class)->getMock();
        $p2p                = $this->getMockBuilder(Path2PathConverter::class)->getMock();
        $p2p->expects(static::once())
            ->method('convert')
            ->with(new IsInstanceOf(RouteInterface::class))
            ->willReturn(['/abc' => ['get' => []]]);
        $merger                = $this->getMockBuilder(RecursiveMerger::class)->getMock();
        $merger->expects(static::once())
            ->method('merge')
            ->with(
                [
                    "openapi"=> "3.0.1",
                    "info"=> [
                        "title"=> "unknown",
                        "version"=> "1.0.0"
                    ]
                ],
                [
                    "paths" => [
                        '/abc' => ['get' => []]
                    ],
                    "info" => []
                ]
            )
            ->willReturn([
                "openapi"=> "3.0.1",
                "info"=> [
                    "title"=> "unknown",
                    "version"=> "1.0.0"
                ],
                "paths" => [
                    '/abc' => ['get' => []]
                ]
            ]);
        $di->expects(static::exactly(2))
            ->method('get')
            ->withConsecutive(
                [Path2PathConverter::class],
                [RecursiveMerger::class]
            )
            ->willReturnOnConsecutiveCalls($p2p, $merger);
        $instance->setDI($di);
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects(static::once())
            ->method('setJsonContent')
            ->with($result)
            ->willReturnSelf();
        $instance->response = $response;
        return $instance;
    }

    /**
     * @test
     * @dataProvider provideIndex
     * @param RouterInterface $router
     * @param string $root
     * @param array $result
     * @return void
     */
    public function testIndex(RouterInterface $router, string $root, array $result)
    {
        $instance = $this->getPreparedInstance($router, $root, $result);
        static::assertSame($instance->response, $instance->index());
    }

    /**
     * @test
     * @dataProvider provideIndex
     * @param RouterInterface $router
     * @param string $root
     * @param array $result
     * @return void
     */
    public function testIndexAction(RouterInterface $router, string $root, array $result)
    {
        $instance = $this->getPreparedInstance($router, $root, $result);
        static::assertSame($instance->response, $instance->indexAction());
    }
}
