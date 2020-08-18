<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Controller;
use De\Idrinth\PhalconRoutes2OpenApi\ServiceProvider;
use PackageVersions\Versions;
use Phalcon\Di;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Router;
use PHPUnit\Framework\TestCase;
use stdClass;

class IntegrationTest extends TestCase {

    public function testRun(): void {
        $di = new Di();
        $request = $this->getMockBuilder(RequestInterface::class)
                ->setMethodsExcept()
                ->getMock();
        $request->expects(self::any())
                ->method('getMethod')
                ->willReturn('GET');
        $request->expects(self::any())
                ->method('getURI')
                ->willReturn('/');
        $router = new Router();
        $router->clear();
        $router->removeExtraSlashes(true);
        $di->set('router', $router);
        $di->set('request', $request);
        $di->set('response', new Response());
        $di->register(new ServiceProvider());
        new Micro($di);
        $controller = new Controller();
        self::assertEquals(
                json_encode([
                    'openapi' => '3.0.1',
                    'paths' => [
                        '/' => [
                            'description' => '',
                            'get' => [
                                'description' => '',
                                'summary' => 'generates api-documentation',
                                'responses' => [
                                    '200' => [
                                        'description' => '',
                                        'content' => [
                                            'application/json' => [
                                                'schema' => [
                                                    'type' => 'object'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'options' => [
                                'description' => '',
                                'summary' => 'Generates an overview over routes registered',
                                'responses' => [
                                    '204' => [
                                        'description' => '',
                                        'content' => [
                                            '*/*' => [
                                                'schema' => [
                                                    'type' => 'string',
                                                    'maxLength' => 0
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'info' => [
                        'title' => Versions::ROOT_PACKAGE_NAME,
                        'version' => Versions::getVersion(Versions::ROOT_PACKAGE_NAME)
                    ]
                ]),
                $controller->index()->getContent()
        );
    }

}
