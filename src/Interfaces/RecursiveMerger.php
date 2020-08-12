<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

/**
 * A service for merging multi-dimensional arrays
 */
interface RecursiveMerger
{
    /**
     * Merges two arrays of any depth
     * @param array $array1
     * @param array $array2
     * @return array
     * @suppress PhanPluginUnknownArrayMethodReturnType, PhanPluginUnknownArrayMethodParamType
     */
    public function merge(array $array1, array $array2): array;

    /**
     * Merges any number of arrays with any depth
     * @param array ...$sets each array to be merged into the first as a parameter
     * @return array
     * @suppress PhanPluginUnknownArrayMethodReturnType
     */
    public function mergeAll(array ...$sets): array;
}
