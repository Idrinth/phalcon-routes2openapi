<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Interfaces;

interface RecursiveMerger
{
    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function merge(array $array1, array $array2):array;
}
