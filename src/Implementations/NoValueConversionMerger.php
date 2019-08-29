<?php declare(strict_types=1);

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

use De\Idrinth\PhalconRoutes2OpenApi\Interfaces\RecursiveMerger;
use InvalidArgumentException;

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
     * @param array[] ...$sets each array to be merged into the first as a parameter
     * @return array
     * @throws InvalidArgumentException
     */
    public function mergeAll(...$sets): array
    {
        $initial = $this->checkArray(array_shift($sets), 1);
        foreach ($sets as $pos => $set) {
            $initial = $this->merge($initial, $this->checkArray($set, $pos+2));
        }
        return $initial;
    }

    /**
     * @param mixed $set
     * @param int $num
     * @throws InvalidArgumentException
     * @return array
     */
    private function checkArray($set, int $num): array
    {
        if (!is_array($set)) {
            throw new InvalidArgumentException("Set #$num is not an array, but a(n) " . gettype($set) . '.');
        }
        return $set;
    }
}
