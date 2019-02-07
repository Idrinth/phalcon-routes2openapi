<?php

namespace De\Idrinth\Test\PhalconRoutes2OpenApi;

use De\Idrinth\PhalconRoutes2OpenApi\Implementations\NoValueConversionMerger;
use De\Idrinth\PhalconRoutes2OpenApi\Implementations\Reflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

class ReflectorTest extends TestCase
{
    private function getTag(string $name, string $description): Generic
    {
        return new Generic($name, new Description($description));
    }

    /**
     * @return array
     */
    public function provideInvoke(): array
    {
        return [
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        '200' => [
                            'description' => 'unknown return',
                            'content' => [
                                '*/*' => [
                                    'schema' => new stdClass()
                                ]
                            ]
                        ]
                    ]
                ],
                []
            ],
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        '200' => [
                            'description' => 'unknown return',
                            'content' => [
                                '*/*' => [
                                    'schema' => new stdClass()
                                ]
                            ]
                        ]
                    ]
                ],
                [$this->getTag('any-tag', "abc qq")]
            ],
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        700 => [
                            'description' => '',
                            'content' => ['abc/def' => [
                                'schema' => new stdClass()
                            ]]
                        ]
                    ]
                ],
                [$this->getTag('return-700', "abc/def")]
            ],
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        700 => [
                            'description' => '',
                            'content' => ['abc/def' => [
                                'schema' => new stdClass()
                            ]]
                        ]
                    ]
                ],
                [$this->getTag('return-700', "abc/def")]
            ],
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        700 => [
                            'description' => '',
                            'content' => [
                                'abc/def' => [
                                    'schema' => new stdClass()
                                ],
                                'abc/defg' => [
                                    'schema' => new stdClass()
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    $this->getTag('return-700', "abc/def"),
                    $this->getTag('return-700', "abc/defg"),
                ]
            ],
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        200 => [
                            'description' => '',
                            'content' => ['*/*' => [
                                'schema' => json_decode('{"type":"string"}')
                            ]]
                        ]
                    ]
                ],
                [$this->getTag('return-200', "{\"type\":\"string\"}")]
            ],
            [
                [
                    "description" => "",
                    "summary" => "",
                    "responses" => [
                        700 => [
                            'description' => '',
                            'content' => ['qq/me' => [
                                'schema' => json_decode('{"type":"string"}')
                            ]]
                        ]
                    ]
                ],
                [$this->getTag('return-700', "qq/me {\"type\":\"string\"}")]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider provideInvoke
     * @return void
     */
    public function testInvoke(array $result, array $tags)
    {
        $block = $this->getMockBuilder(DocBlock::class)->getMock();
        $block->expects(static::once())
            ->method('getTags')
            ->with()
            ->willReturn($tags);
        $doc = $this->getMockBuilder(DocBlockFactoryInterface::class)->getMock();
        $doc->expects(static::once())
            ->method('create')
            ->with()
            ->willReturn($block);
        static::assertEquals(
            $result,
            (new Reflector(
                $doc,
                new NoValueConversionMerger()
            ))(Reflector::class, '__invoke')
        );
    }
}
