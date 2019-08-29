<?php declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\PathTargetAnnotationResolver;
use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use ReflectionClass;
use stdClass;
use Exception;

class Reflector implements PathTargetAnnotationResolver
{
    /**
     * @var array
     */
    private $cache = [];

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
     * @return array
     */
    public function __invoke(string $class, string $method):array
    {
        try {
            if (!isset($this->cache[$class])) {
                $this->cache[$class]['#'] = new ReflectionClass($class);
            }
            if (!isset($this->cache[$class][$method])) {
                $this->cache[$class][$method] = DefaultResponse::add(
                    $this->getReflect($this->cache[$class]['#'], $method)
                );
            }
        } catch (Exception $e) {
            $this->cache[$class][$method] = DefaultResponse::add([
                "summary" => 'unretrievable definition',
                "description" => "$class::$method could not be reflected on.",
            ]);
        }
        return $this->cache[$class][$method];
    }

    /**
     * @param ReflectionClass $class
     * @param string $method
     * @return array
     */
    private function getReflect(ReflectionClass $class, string $method):array
    {
        $docBlock = $this->parser->create($class->getMethod($method));
        $data = [];
        foreach ($docBlock->getTags() as $tag) {
            if ((int) preg_match('/^return-([1-9][0-9]{2})$/', $tag->getName(), $matches) > 0) {
                $parts = explode(" ", "$tag", 2);
                if (!isset($parts[0]) || $parts[0] === '' || $parts[0]{0} === '{') {
                    array_unshift($parts, '*/*');
                    if (isset($parts[2])) {
                        $parts[1] .= " " . array_pop($parts);
                    }
                }
                $data[$matches[1]] = $this->merger->merge(
                    $data[$matches[1]]??[],
                    [
                        "description" => '',
                        "content" => [
                            $parts[0] => [
                                "schema" => json_decode($parts[1] ?? '{}') ?: new stdClass()
                            ]
                        ]
                    ]
                );
            }
        }
        return [
            "description" => $docBlock->getDescription().'',
            "summary" => $docBlock->getSummary().'',
            "responses" => $data
        ];
    }
}
