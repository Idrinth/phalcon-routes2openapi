<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Reflector;
use PHPUnit\Framework\TestCase;
use stdClass;

class ReflectorTest extends TestCase
{
    public function testInvoke()
    {
        $this->assertEquals(
            [
                "description" => "",
                "summary" => "Tries to find references to return codes in the method's phpdoc",
                "responses" => [
                    "200" => [
                        "description" => "",
                        "content" => [
                            'application/json' => new stdClass(),
                            'text/json' => new stdClass(),
                        ]
                    ],
                    "204" => [
                        "description" => "",
                        "content" => [
                            '*/*' => new stdClass(),
                        ]
                    ],
                    "500" => [
                        "description" => "",
                        "content" => [
                            '*/*' => new stdClass(),
                        ]
                    ]
                ]
            ],
            (new Reflector())(Reflector::class, '__invoke')
        );
    }
}