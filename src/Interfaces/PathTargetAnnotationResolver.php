<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

interface PathTargetAnnotationResolver
{
    /**
     * Tries to find references to return codes in the method's phpdoc
     * @param string $class
     * @param string $method
     * @return array
     */
    public function __invoke(string $class, string $method):array;
}