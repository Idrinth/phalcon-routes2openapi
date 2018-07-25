<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Reflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function testInvoke()
    {
        $doc = $this->getMockBuilder(DocBlockFactoryInterface::class)->getMock();
        $doc->expects(static::once())
            ->method('create')
            ->with()
            ->willReturn(new DocBlock());
        static::assertEquals(
            [
                "description" => "",
                "summary" => "",
                "responses" => []
            ],
            (new Reflector(
                $doc,
                new NoValueConversionMerger()
            ))(Reflector::class, '__invoke')
        );
    }
}
