<?php

namespace Roke\PhpFactory;

use Faker\Factory;

class DynamicFactory
{
    /**
     * Metodo para crear un objeto con valores aleatorios
     *
     * @param string $object
     * @return object
     */
    public static function create(string $object)
    {
        $arrayValues = self::createDataArray($object);
        return ArrayToObject::make($arrayValues, $object);
    }

    /**
     * Metodo para crear un array con los valores de los parametros del constructor
     *
     * @param string $object
     * @return array
     */
    protected static function createDataArray(string $object)
    {
        $faker = Factory::create();
        $arrayValues = [];
        $values = self::getConstructorAnnotations($object);
        $constructor = self::getConstructorParameters($object, $values);
        foreach ($constructor->getParameters() as $parameter) {
            $nameParameter = $parameter->getName();
            $typeParameter = $parameter->getType();

            if (isset($values[$nameParameter])) {
                $randomKey = array_rand($values[$nameParameter]);
                $arrayValues[$nameParameter] = $values[$nameParameter][$randomKey];
                continue;
            }

            if (($typeParameter === null) || ($typeParameter !== null && in_array($typeParameter->getName(), ['string', 'int', 'bool', 'float', 'array']))) {
                $arrayValues[$nameParameter] = self::getRandomValue($typeParameter, $faker);
                continue;
            }

            $arrayValues[$nameParameter] = self::createDataArray($typeParameter->getName());

        }
        return $arrayValues;
    }

    /**
     * Metodo para obtener un array de los parametros que tiene el constructor de la clase
     *
     * @param string $class
     * @return ?ReflectionMethod
     */
    protected static function getConstructorParameters(string $class, array $values)
    {
        $reflection = new \ReflectionClass($class);
        return $reflection->getConstructor();
    }

    /**
     * Metodo para obtener un array de los parametros que tiene el constructor de la clase
     *
     * @param string $class
     * @return array
     */
    protected static function getConstructorAnnotations(string $class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        $docComment = $constructor->getDocComment();
        $docComment = str_replace(['/**', '*/'], '', $docComment);
        $docComment = trim($docComment);
        $docComment = explode("\n", $docComment);
        $docComment = array_map(function ($item) {
            //si contiene la palabra @value devuelvo el valor
            if (strpos($item, '@value') !== false) {
                return substr(trim(str_replace(['@value', '*'], '', $item)), 1);
            }
        }, $docComment);
        $docComment = array_filter($docComment, function ($item) {
            return !empty($item);
        });
        $docComment = array_values($docComment);
        return self::constructorAnnotationsAsArray($docComment);
    }

    /**
     * Metodo para convertir un array de anotaciones en un array asociativo
     *
     * @param array $annotations
     * @return array
     */
    protected static function constructorAnnotationsAsArray(array $annotations)
    {
        $array = [];
        foreach ($annotations as $annotation) {
            $firstSpace = strpos($annotation, ' ');
            $key = substr($annotation, 0, $firstSpace);
            $value = substr($annotation, $firstSpace + 1);
            $array[$key] = eval("return $value;");
        }

        return $array;
    }

    /**
     * Metodo para obtener un valor aleatorio segun el tipo
     *
     * @param string|null $type
     * @param \Faker\Generator $faker
     * @return mixed
     */
    protected static function getRandomValue(?string $type, $faker)
    {
        switch ($type) {
            case 'int':
                return $faker->randomDigit;
            case 'string':
                return $faker->word();
            case 'array':
                return $faker->randomElements;
            case 'bool':
                return $faker->randomElement([true, false]);
            case 'float':
                return $faker->randomFloat;
            default:
                return $faker->randomElement([true, false, $faker->randomDigit, $faker->word, $faker->randomFloat]);
        }
    }
}
