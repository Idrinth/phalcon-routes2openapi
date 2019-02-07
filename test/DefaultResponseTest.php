<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\DefaultResponse;
use PHPUnit\Framework\TestCase;

class DefaultResponseTest extends TestCase
{
    /**
     * @return array
     */
    public function provideAdd(): array
    {
        $default = ['summary' => '', 'description' => '', 'responses' => ['200' => ['description' => 'unknown return','content' => ['*/*' => ['schema' => new \stdClass()]]]]];
        return [
            'empty' => [[], $default],
            'existing summary' => [['summary' => 'a'], array_merge($default, ['summary' => 'a'])],
            'existing description' => [['description' => 'b'], array_merge($default, ['description' => 'b'])],
            'existing response' => [['responses' => ['200' => []]], array_merge($default, ['responses' => ['200' => []]])],
        ];
    }

    /**
     * @dataProvider provideAdd
     * @param array $route
     * @param array $expectation
     * @return void
     */
    public function testAdd(array $route, array $expectation): void
    {
        self::assertEquals($expectation, DefaultResponse::add($route));
    }
}
