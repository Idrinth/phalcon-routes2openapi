<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Controller;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2Path;
use Phalcon\DiInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Mvc\RouterInterface;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{

    private function buildRouterMock(): RouterInterface
    {
        $router = $this->getMockBuilder(RouterInterface::class)->getMock();
        $router->expects($this->exactly(1))
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
        $p2p                = $this->getMockBuilder(Path2Path::class)->getMock();
        $p2p->expects($this->once())
            ->method('convert')
            ->with(new IsInstanceOf(RouteInterface::class))
            ->willReturn(['/abc' => ['get' => []]]);
        $di->expects($this->once())->method('get')->with(Path2Path::class)->willReturn($p2p);
        $instance->setDI($di);
        $response           = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects($this->once())->method('setJsonContent')->with($result)->willReturnSelf();
        $instance->response = $response;
        return $instance;
    }

    /**
     * @test
     * @dataProvider provideIndex
     */
    public function testIndex(RouterInterface $router, string $root,
                              array $result)
    {
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getPreparedInstance($router, $root, $result)->index()
        );
    }

    /**
     * @test
     * @dataProvider provideIndex
     */
    public function testIndexAction(RouterInterface $router, string $root,
                                    array $result)
    {
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getPreparedInstance($router, $root, $result)->indexAction()
        );
    }
}