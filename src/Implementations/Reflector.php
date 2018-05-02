<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

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
        if(!isset($this->cache[$class])) {
            $this->cache[$class]['____class'] = new ReflectionClass($class);
        }
        if(!isset($this->cache[$class][$method])) {
            $this->cache[$class][$method] = $this->getReflect($this->cache[$class]['____class'], $method);
        }
        return $this->cache[$class][$method];
    }
    private function getReflect(ReflectionClass $class, string $method)
    {
        $docBlock = $this->parser->create($class->getMethod($method));
        $data = [];
        foreach ($docBlock->getTags() as $tag) {
            if(preg_match('/^return-([1-9][0-9]{2})$/', $tag->getName(), $matches)) {
                $parts = explode(" ", "$tag", 2);
                $data[$matches[1]] = $this->arrayMergeRecursiveNoConversion(
                    $data[$matches[1]]??[],
                    [
                        "description" => '',
                        "content" => [
                            $parts[0]?:'*/*' => json_decode($parts[1]??'{}')
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
    private function handleValueMerge(array $array1, $value, $key)
    {
        if (!isset($array1[$key])) {
            return $value;
        }
        if (is_array($value) && is_array($array1[$key])) {
            return $this->arrayMergeRecursiveNoConversion($array1[$key], $value);
        }
        return $value;
    }
    private function arrayMergeRecursiveNoConversion(array $array1, array $array2)
    {
      foreach ($array2 as $key => $value)
      {
        $array1[$key] = $this->handleValueMerge($array1, $value, $key);
      }
      return $array1;
    }
}