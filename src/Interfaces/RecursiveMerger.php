<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

interface RecursiveMerger
{
    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function merge(array $array1, array $array2): array;

    /**
     * @param array ...$sets each array to be merged into the first as a parameter
     * @return array
     */
    public function mergeAll(...$sets): array;
}
