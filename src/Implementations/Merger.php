<?php

namespace De\Idrinth\PhalconRoutes2OpenApi\Implementations;

class Merger
{
    private static function handleValueMerge(array $array1, $value, $key)
    {
        if (!isset($array1[$key])) {
            return $value;
        }
        if (is_array($value) && is_array($array1[$key])) {
            return self::arrayMergeRecursiveNoConversion($array1[$key], $value);
        }
        return $value;
    }
    public static function arrayMergeRecursiveNoConversion(array $array1, array $array2)
    {
      foreach ($array2 as $key => $value)
      {
        $array1[$key] = self::handleValueMerge($array1, $value, $key);
      }
      return $array1;
    }
}