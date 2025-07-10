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
            $paramTypeName = self::getParameterTypeName($paramType);

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
                // Aplicar conversión de tipos explícita
                $params[] = self::convertValueToType($value, $paramTypeName, $parameter->allowsNull());
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

    /**
     * Converts a value to the expected type.
     *
     * @param mixed $value The value to convert.
     * @param string|null $typeName The expected type name.
     * @param bool $allowsNull Whether the parameter allows null values.
     * @return mixed The converted value.
     */
    protected static function convertValueToType($value, ?string $typeName, bool $allowsNull)
    {
        // CRÍTICO: Si el valor es null y se permite null, retornar null inmediatamente
        if ($value === null && $allowsNull) {
            return null;
        }

        // Si no hay tipo especificado, retornar el valor tal como está
        if ($typeName === null) {
            return $value;
        }

        // Conversiones de tipos específicas mejoradas
        switch ($typeName) {
            case 'int':
                if (is_int($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return (int) $value;
                }
                if (is_string($value) && is_numeric($value)) {
                    return (int) $value;
                }
                if (is_bool($value)) {
                    return $value ? 1 : 0;
                }
                return $allowsNull ? null : 0;
            
            case 'float':
                if (is_float($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return (float) $value;
                }
                if (is_string($value) && is_numeric($value)) {
                    return (float) $value;
                }
                return $allowsNull ? null : 0.0;
            
            case 'bool':
                if (is_bool($value)) {
                    return $value;
                }
                if (is_int($value)) {
                    return $value !== 0;
                }
                if (is_string($value)) {
                    $lower = strtolower(trim($value));
                    if (in_array($lower, ['true', '1', 'yes', 'on', 'active'])) {
                        return true;
                    }
                    if (in_array($lower, ['false', '0', 'no', 'off', 'inactive', ''])) {
                        return false;
                    }
                    // Para strings numéricos
                    if (is_numeric($value)) {
                        return (float) $value !== 0.0;
                    }
                }
                return $allowsNull ? null : false;
            
            case 'string':
                if (is_string($value)) {
                    return $value;
                }
                if ($value === null) {
                    return $allowsNull ? null : '';
                }
                if (is_scalar($value)) {
                    return (string) $value;
                }
                if (is_array($value) || is_object($value)) {
                    return $allowsNull ? null : '';
                }
                return $allowsNull ? null : '';
            
            case 'array':
                if (is_array($value)) {
                    return $value;
                }
                if (is_string($value) && !empty($value)) {
                    // Intentar decodificar JSON
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded;
                    }
                }
                return $allowsNull ? null : [];
            
            default:
                // Para tipos desconocidos, retornar el valor tal como está
                return $value;
        }
    }

    /**
     * Gets the parameter type name, handling Union types like ?string
     *
     * @param \ReflectionType|null $paramType
     * @return string|null
     */
    protected static function getParameterTypeName(?\ReflectionType $paramType): ?string
    {
        if ($paramType === null) {
            return null;
        }

        // Handle Union types (like ?string which is string|null)
        if ($paramType instanceof \ReflectionUnionType) {
            $types = $paramType->getTypes();
            foreach ($types as $type) {
                if ($type->getName() !== 'null') {
                    return $type->getName();
                }
            }
            return null;
        }

        // Handle Named types (like string, int, float, etc.)
        if ($paramType instanceof \ReflectionNamedType) {
            return $paramType->getName();
        }

        // Fallback for other types
        return (string) $paramType;
    }
}
