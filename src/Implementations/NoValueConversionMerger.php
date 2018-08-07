<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;

class NoValueConversionMerger implements RecursiveMerger
{
    /**
     * @param array $array1
     * @param mixed $value
     * @param int|string $key
     * @return mixed
     */
    private function handleValueMerge(array $array1, $value, $key)
    {
        if (!isset($array1[$key])) {
            return $value;
        }
        if (is_array($value) && is_array($array1[$key])) {
            return $this->merge($array1[$key], $value);
        }
        return $value;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function merge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            is_integer($key) ? $array1[] = $value : $array1[$key] = $this->handleValueMerge($array1, $value, $key);
        }
        return $array1;
    }

    /**
     * @param array $sets each array to be merged into the first as a parameter
     * @return array
     */
    public function mergeAll(...$sets): array
    {
        $initial = array_shift($sets);
        foreach ($sets as $set)
        {
            $initial = $this->merge($initial, $set);
        }
        return $initial;
    }
}
