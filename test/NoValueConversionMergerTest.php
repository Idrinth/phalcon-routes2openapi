<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use PHPUnit\Framework\TestCase;

class NoValueConversionMergerTest extends TestCase
{
    public function provideMerge()
    {
        return [
            [[], [], []],
            [['abc'], ['qq'], ['abc', 'qq']],
            [['a' => ['zz']], ['abv', 'a' => [11]], ['a' => ['zz', 11], 'abv']],
            [['a' => ['zz' => 1]], ['abv', 'a' => []], ['a' => ['zz' => 1], 'abv']],
            [['b' => ['a' => ['zz' => 1]]], ['abv', 'b' => ['a' => []]], ['b' => ['a' => ['zz' => 1]], 'abv']],
            [['abv', 'b' => ['a' => []]], ['b' => ['a' => ['zz' => 1]]], ['b' => ['a' => ['zz' => 1]], 'abv']],
        ];
    }
    /**
     * @dataProvider provideMerge
     * @param array $in1
     * @param array $in2
     * @param array $out
     */
    public function testMerge(array $in1, array $in2, array $out)
    {
        $this->assertEquals(
            $out,
            (new NoValueConversionMerger)->merge($in1, $in2)
        );
    }
}