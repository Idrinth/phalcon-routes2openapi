<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use Exception;

class Reflector
{
    /**
     * @var array
     */
    private $cache = [];
    /**
     *
     * @var DocBlockFactory
     */
    private $parser;
    public function __construct()
    {
        $this->parser = DocBlockFactory::createInstance();
    }
    /**
     * Tries to find references to return codes in the method's phpdoc
     * @param string $class
     * @param string $method
     * @return array
     * @return-200 application/json
     * @return-200 text/json
     * @return-204
     * @return-500
     */
    public function __invoke(string $class, string $method):array
    {
        try {
            if(!isset($this->cache[$class])) {
                $this->cache[$class]['____class'] = new ReflectionClass($class);
            }
            if(!isset($this->cache[$class][$method])) {
                $this->cache[$class][$method] = $this->getReflect($this->cache[$class]['____class'], $method);
            }
            return $this->cache[$class][$method];
        } catch(Exception $e) {
            return [];
        }
    }
    private function getMimeAndSchemaFromTag($tag)
    {
        if(!preg_match('#^[a-z0-9.\-_]+/[a-z0-9.\-_]+($|\s)#', $tag)) {
            return ['*/*', $tag];
        }
        return explode(" ", $tag, 2);
    }
    private function getReflect(ReflectionClass $class, string $method)
    {
        $docBlock = $this->parser->create($class->getMethod($method));
        $data = [];
        foreach ($docBlock->getTags() as $tag) {
            if(preg_match('/^return-([1-9][0-9]{2})$/', $tag->getName(), $code)) {
                $parts = $this->getMimeAndSchemaFromTag("$tag");
                $data[$code[1]] = Merger::arrayMergeRecursiveNoConversion(
                    $data[$code[1]]??[],
                    [
                        "description" => '',
                        "content" => [
                            $parts[0]?:'*/*' => [
                                "schema" => json_decode($parts[1]??'{}')
                            ]
                        ]
                    ]
                );
            }
        }
        return [
            "description" => $docBlock->getDescription().'',
            "summary" => $docBlock->getSummary(),
            "responses" => $data
        ];
    }
}
