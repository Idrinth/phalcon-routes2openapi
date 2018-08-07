<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use PHPUnit\Framework\TestCase;

class NoValueConversionMergerTest extends TestCase
{
    /**
     * @return array
     */
    public function provideMerge(): array
    {
        return [
            [[], [], []],
            [['abc'], ['qq'], ['abc', 'qq']],
            [['abc' => ['a']], ['abc' => 1], ['abc' => 1]],
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
     * @return void
     */
    public function testMerge(array $in1, array $in2, array $out)
    {
        static::assertEquals(
            $out,
            (new NoValueConversionMerger)->merge($in1, $in2)
        );
    }

    /**
     * @dataProvider provideMerge
     * @param array $in1
     * @param array $in2
     * @param array $out
     * @return void
     */
    public function testMergeAll()
    {
        static::assertEquals(
            ['a' => [1, 2, 4, 'z' => 77], '1hh'],
            (new NoValueConversionMerger)->mergeAll(
                ['a' => [1, 2]],
                ['a' => [4, 'z' => 11]],
                ['a' => ['z' => 77], '1hh']
            )
        );
    }
}
