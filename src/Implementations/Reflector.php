<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\PathTargetAnnotationResolver;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use Exception;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use ReflectionClass;
use stdClass;

/**
 * Uses Reflection to read out method docs and contained route hints
 */
final class Reflector implements PathTargetAnnotationResolver
{
    /**
     * @var array<string, array<string, array>>
     */
    private $cache = [];

    /**
     * @var array<string, ReflectionClass>
     */
    private $classes = [];

    /**
     * @var DocBlockFactoryInterface
     */
    private $parser;

    /**
     * @var RecursiveMerger
     */
    private $merger;

    /**
     * @param DocBlockFactoryInterface $parser
     * @param RecursiveMerger $merger
     */
    public function __construct(DocBlockFactoryInterface $parser, RecursiveMerger $merger)
    {
        $this->parser = $parser;
        $this->merger = $merger;
    }

    /**
     * Tries to find references to return codes in the method's phpdoc
     * @param string $class
     * @param string $method
     * @return array<string, array>
     */
    public function __invoke(string $class, string $method): array
    {
        try {
            $this->classes[$class] = $this->classes[$class] ?? new ReflectionClass($class);
            if (!isset($this->cache[$class][$method])) {
                $this->cache[$class][$method] = DefaultResponse::add(
                    $this->getReflect($this->classes[$class], $method)
                );
            }
        } catch (Exception $e) {
            $this->cache[$class][$method] = DefaultResponse::add([
                'summary' => 'unretrievable definition',
                'description' => "$class::$method could not be reflected on.\n$e",
            ]);
        }
        return $this->cache[$class][$method];
    }

    /**
     * Adds default
     * @param Tag $tag
     * @return string[]
     */
    private function addDefaultParts(Tag $tag): array
    {
        $parts = explode(" ", "$tag", 2);
        if (!isset($parts[0]) || $parts[0] === '' || $parts[0][0] === '{') {
            array_unshift($parts, '*/*');
            if (isset($parts[2])) {
                $parts[1] .= " " . array_pop($parts);
            }
        }
        return $parts;
    }

    /**
     * Retrieve tags from doc block
     * @param DocBlock $docBlock
     * @return array<string, string>|array<string, array>
     */
    private function getDocBlockData(DocBlock $docBlock): array
    {
        $data = [];
        foreach ($docBlock->getTags() as $tag) {
            if ((int) preg_match('/^return-([1-9][0-9]{2})$/', $tag->getName(), $matches) > 0) {
                $parts = $this->addDefaultParts($tag);
                $data[$matches[1]] = $this->merger->merge(
                    $data[$matches[1]] ?? [],
                    [
                        'description' => '',
                        'content' => [
                            $parts[0] => [
                                'schema' => json_decode($parts[1] ?? '{}') ?: new stdClass()
                            ]
                        ]
                    ]
                );
            }
        }
        return $data;
    }

    /**
     * Retrieve doc block information and build route data from it
     * @param ReflectionClass $class
     * @param string $method
     * @return array<string, string>|array<string, array>
     */
    private function getReflect(ReflectionClass $class, string $method): array
    {
        $docBlock = $this->parser->create($class->getMethod($method)->getDocComment());
        $data = $this->getDocBlockData($docBlock);
        return [
            'description' => $docBlock->getDescription() . '',
            'summary' => $docBlock->getSummary() . '',
            'responses' => $data
        ];
    }
}
