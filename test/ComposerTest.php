<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Composer;
use PHPUnit\Framework\TestCase;

class ComposerTest extends TestCase
{
    public function testInvoke()
    {
        $this->assertEquals(
            [
                "title" => "idrinth/phalcon-routes2openapi",
                "description" => "Generates an JSON represantation of the routes "
                    . "registered via phalcon in an OpenAPI-compatible way."
            ],
            (new Composer())(dirname(__DIR__).'/composer.json')
        );
    }
}
