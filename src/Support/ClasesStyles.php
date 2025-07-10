<?php

namespace Roke\PhpFactory\Support;

class ClasesStyles
{
    /**
     * Transform keys to snake_case.
     *
     * @param array $array Array data to transform
     * @return array Array with transformed keys
     */
    public static function snake(array $array): array
    {
        $newArray = [];
        foreach ($array as $index => $element) {
            $newArray[Str::snake((string) $index)] = $element;
        }
        return $newArray;
    }

    /**
     * Transform keys to camelCase.
     *
     * @param array $array Array data to transform
     * @return array Array with transformed keys
     */
    public static function camel(array $array): array
    {
        $newArray = [];
        foreach ($array as $index => $element) {
            $newArray[Str::camel((string) $index)] = $element;
        }
        return $newArray;
    }

    /**
     * Transform keys to StudlyCase.
     *
     * @param array $array Array data to transform
     * @return array Array with transformed keys
     */
    public static function studly(array $array): array
    {
        $newArray = [];
        foreach ($array as $index => $element) {
            $newArray[Str::studly((string) $index)] = $element;
        }
        return $newArray;
    }

    
}