<?php

declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;

/**
 * Merges multidimensional arrays
 */
class NoValueConversionMerger implements RecursiveMerger
{
    /**
     * Handles the overwriting and merging of values
     * @suppress PhanPluginUnknownArrayMethodParamType
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
     * Merges two multidimensional arrays
     * @suppress PhanPluginUnknownArrayMethodParamType, PhanPluginUnknownArrayMethodReturnType
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function merge(array $array1, array $array2): array
    {
        $isNumericKeyed = $this->isNumericKeyed($array1, $array2);
        foreach ($array2 as $key => $value) {
            $isNumericKeyed ? $array1[] = $value : $array1[$key] = $this->handleValueMerge($array1, $value, "$key");
        }
        return $array1;
    }

    /**
     * @param array<mixed, mixed> $array1
     * @param array<mixed, mixed> $array2
     * @return bool
     */
    public function isNumericKeyed(array $array1, array $array2): bool
    {
        if (implode('#', array_keys($array1)) !== implode('#', array_keys(array_values($array1)))) {
            return false;
        }
        return implode('#', array_keys($array2)) === implode('#', array_keys(array_values($array2)));
    }

    /**
     * Merges any number of multidimensional arrays with later overwriting earlier
     * @suppress PhanPluginUnknownArrayMethodParamType, PhanPluginUnknownArrayMethodReturnType
     * @param array[] ...$sets each array to be merged into the first as a parameter
     * @return array
     */
    public function mergeAll(array ...$sets): array
    {
        $initial = array_shift($sets);
        foreach ($sets as $set) {
            $initial = $this->merge($initial, $set);
        }
        return $initial;
    }
}
