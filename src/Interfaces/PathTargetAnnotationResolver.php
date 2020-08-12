<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

/**
 * A service to resolve annotations in phpdoc to openapi paths
 */
interface PathTargetAnnotationResolver
{
    /**
     * Tries to find references to return codes in the method's phpdoc
     * @param string $class
     * @param string $method
     * @return array<string, array>
     */
    public function __invoke(string $class, string $method): array;
}
