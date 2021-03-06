<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Controller;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\Path2PathConverter;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use PackageVersions\Versions;
use Phalcon\Di\DiInterface;
use Phalcon\Http\RequestInterface;
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
            ->willReturn([
                $this->getMockBuilder(RouteInterface::class)->getMock()
            ]);
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
                [
                    'openapi' => '3.0.1',
                    'info' => [
                        'title' => Versions::ROOT_PACKAGE_NAME,
                        'version' => Versions::getVersion(Versions::ROOT_PACKAGE_NAME)
                    ],
                    'paths' => [
                        '/abc' => [
                            'get' => []
                        ]
                    ]
                ]
            ],
            [
                $this->buildRouterMock(),
                [
                    'openapi' => '3.0.1',
                    'info' => [
                        'title' => Versions::ROOT_PACKAGE_NAME,
                        'version' => Versions::getVersion(Versions::ROOT_PACKAGE_NAME)
                    ],
                    'paths' => [
                        '/abc' => [
                            'get' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function provideOptions(): array
    {
        return [
            [
                '',
            ],
            [
                'http://abc.de',
            ],
        ];
    }

    /**
     * @param DiInterface $serviceContainer
     * @return Controller
     */
    private function getBasePreparedInstance(DiInterface $serviceContainer): Controller
    {
        $instance           = new Controller();
        $instance->request  = $this->getMockBuilder(RequestInterface::class)->getMock();
        $instance->setDI($serviceContainer);
        $instance->response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $instance->response->expects(static::exactly(2))
            ->method('setHeader')
            ->willReturnSelf();
        return $instance;
    }

    /**
     * @param RouterInterface $router
     * @param array $result
     * @return Controller
     */
    private function getPreparedInstance(RouterInterface $router, array $result): Controller
    {
        $serviceContainer   = $this->getMockBuilder(DiInterface::class)->getMock();
        $p2p                = $this->getMockBuilder(Path2PathConverter::class)->getMock();
        $p2p->expects(static::once())
            ->method('convert')
            ->with(new IsInstanceOf(RouteInterface::class))
            ->willReturn(['/abc' => ['get' => []]]);
        $merger                = $this->getMockBuilder(RecursiveMerger::class)->getMock();
        $merger->expects(static::never())
            ->method('merge');
        $merger->expects(static::once())
            ->method('mergeAll')
            ->with(['/abc' => ['get' => []]])
            ->willReturn(['/abc' => ['get' => []]]);
        $serviceContainer->expects(static::exactly(2))
            ->method('get')
            ->withConsecutive(
                [Path2PathConverter::class],
                [RecursiveMerger::class]
            )
            ->willReturnOnConsecutiveCalls($p2p, $merger);
        $instance           = $this->getBasePreparedInstance($serviceContainer);
        $instance->router   = $router;
        $instance->response->expects(static::once())
            ->method('setJsonContent')
            ->with($result)
            ->willReturnSelf();
        return $instance;
    }

    /**
     * @test
     * @dataProvider provideIndex
     * @param RouterInterface $router
     * @param array $result
     * @return void
     */
    public function testIndex(RouterInterface $router, array $result)
    {
        $instance = $this->getPreparedInstance($router, $result);
        static::assertSame($instance->response, $instance->index());
    }

    /**
     * @test
     * @dataProvider provideIndex
     * @param RouterInterface $router
     * @param array $result
     * @return void
     */
    public function testIndexAction(RouterInterface $router, array $result)
    {
        $instance = $this->getPreparedInstance($router, $result);
        static::assertSame($instance->response, $instance->indexAction());
    }

    /**
     * @param Controller $instance
     * @param string $origin
     * @return void
     */
    private function prepareInstanceForOptions(Controller $instance, string $origin)
    {
        $instance->request
            ->expects(self::once())
            ->method('getHeader')
            ->with('Origin')
            ->willReturn($origin);
        $instance->response
            ->expects(static::once())
            ->method('setStatusCode')
            ->with(204)
            ->willReturnSelf();
        $instance->response
            ->expects(static::exactly(2))
            ->method('setHeader')
            ->withConsecutive(
                ['Access-Control-Allow-Origin', $origin === '' ? '*' : $origin],
                ['Access-Control-Allow-Methods', 'GET, OPTIONS']
            )
            ->willReturnSelf();
    }

    /**
     * @test
     * @dataProvider provideOptions
     * @param string $origin
     * @return void
     */
    public function testOptions(string $origin)
    {
        $instance = $this->getBasePreparedInstance($this->getMockBuilder(DiInterface::class)->getMock());
        $this->prepareInstanceForOptions($instance, $origin);
        static::assertSame($instance->response, $instance->options());
    }

    /**
     * @test
     * @dataProvider provideOptions
     * @param string $origin
     * @return void
     */
    public function testOptionsAction(string $origin)
    {
        $instance = $this->getBasePreparedInstance($this->getMockBuilder(DiInterface::class)->getMock());
        $this->prepareInstanceForOptions($instance, $origin);
        static::assertSame($instance->response, $instance->optionsAction());
    }
}
