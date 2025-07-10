<?php

namespace Roke\PhpFactory;

use ReflectionClass;
use Roke\PhpFactory\Support\ClasesStyles;

class ArrayToObject
{
    public const SNAKE = "snake";
    public const CAMEL = "camel";
    public const STUDLY = "studly";
    public const NONE = "none";

    /**
     * Creates an object from an array.
     *
     * @param array $array The data array.
     * @param string $class The target class name.
     * @param string $fromCase The case style of the input array keys. Defaults to 'snake'.
     * @return object An instance of the target class.
     * @throws \ReflectionException
     */
    public static function make(array $array, string $class, string $fromCase = self::SNAKE): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $constructorParams = self::buildConstructorParameters($array, $constructor, $fromCase);

        return $reflection->newInstanceArgs($constructorParams);
    }

    /**
     * Normalizes array keys to camelCase from a specified case style.
     *
     * @param array $array
     * @param string $fromCase
     * @return array
     */
    protected static function normalizeKeys(array $array, string $fromCase): array
    {
        // The goal is always to get to camelCase for the constructor parameters.
        switch ($fromCase) {
            case self::SNAKE:
            case self::STUDLY:
                return ClasesStyles::camel($array);
            case self::CAMEL:
            case self::NONE:
            default:
                // Assume keys are already in the correct format, do nothing.
                return $array;
        }
    }

    /**
     * Builds the array of arguments for the constructor.
     *
     * @param array $data The raw data array.
     * @param \ReflectionMethod $constructor The constructor reflection object.
     * @param string $fromCase Original case style for recursive calls.
     * @return array
     * @throws \ReflectionException
     */
    protected static function buildConstructorParameters(array $data, \ReflectionMethod $constructor, string $fromCase): array
    {
        $normalizedData = self::normalizeKeys($data, $fromCase);
        $params = [];
        foreach ($constructor->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            $paramType = $parameter->getType();
            $paramTypeName = $paramType ? $paramType->getName() : null;

            $value = $normalizedData[$paramName] ?? null;

            if ($value === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $params[] = $parameter->getDefaultValue();
                    continue;
                }
                if ($parameter->allowsNull()) {
                    $params[] = null;
                    continue;
                }
            }

            if ($paramTypeName === 'array' && is_array($value)) {
                $docCommentClass = self::getArrayDocCommentClass($constructor, $paramName);
                if ($docCommentClass) {
                    $subObjects = [];
                    foreach ($value as $itemData) {
                        if (is_array($itemData)) {
                            $subObjects[] = self::make($itemData, $docCommentClass, $fromCase);
                        }
                    }
                    $params[] = $subObjects;
                } else {
                    $params[] = $value;
                }
            } elseif ($paramTypeName && class_exists($paramTypeName) && is_array($value)) {
                $params[] = self::make($value, $paramTypeName, $fromCase);
            } else {
                $params[] = $value;
            }
        }
        return $params;
    }

    /**
     * Gets the class name from a `@param array<ClassName>` annotation.
     *
     * @param \ReflectionMethod $constructor
     * @param string $paramName
     * @return string|null
     */
    protected static function getArrayDocCommentClass(\ReflectionMethod $constructor, string $paramName): ?string
    {
        $doc = $constructor->getDocComment();
        if ($doc === false) {
            return null;
        }

        $pattern = '/@param\s+array<([^>]+)>\s+\$' . $paramName . '/';
        if (preg_match($pattern, $doc, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
