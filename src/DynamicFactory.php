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
    public static function create(string $object, array $valuesUser = []): object
    {
        $arrayValues = self::createDataArray($object, $valuesUser);
        return ArrayToObject::make($arrayValues, $object);
    }

    /**
     * Metodo para crear un array con los valores de los parametros del constructor
     *
     * @param string $object
     * @return array
     */
    protected static function createDataArray(string $object, array $valuesUser): array
    {
        $faker = Factory::create();
        $arrayValues = [];
        $valuesAnnotations = self::getConstructorAnnotations($object);
        $constructor = self::getConstructorParameters($object);
        foreach ($constructor->getParameters() as $parameter) {
            $nameParameter = $parameter->getName();
            $typeParameter = $parameter->getType();

            if (isset($valuesUser[$nameParameter])) {
                if (($typeParameter === null) || ($typeParameter !== null && in_array($typeParameter->getName(), ['string', 'int', 'bool', 'float', 'array']))) {
                    $arrayValues[$nameParameter] = $valuesUser[$nameParameter];
                    continue;
                }
            }

            if (isset($valuesAnnotations['value'][$nameParameter])) {
                $randomKey = array_rand($valuesAnnotations['value'][$nameParameter]);
                $arrayValues[$nameParameter] = $valuesAnnotations['value'][$nameParameter][$randomKey];
                continue;
            }

            if (isset($valuesAnnotations['faker'][$nameParameter])) {
                $fakerMethod = "return \$faker->".str_replace("\$faker->", '', $valuesAnnotations["faker"][$nameParameter]).";";
                $arrayValues[$nameParameter] = eval($fakerMethod);
                continue;
            }

            if (($typeParameter === null) || ($typeParameter !== null && in_array($typeParameter->getName(), ['string', 'int', 'bool', 'float', 'array']))) {
                $arrayValues[$nameParameter] = self::getRandomValue($typeParameter, $faker);
                continue;
            }

            $arrayValues[$nameParameter] = self::createDataArray($typeParameter->getName(), $valuesUser[$nameParameter] ?? []);

        }
        return $arrayValues;
    }

    /**
     * Metodo para obtener un array de los parametros que tiene el constructor de la clase
     *
     * @param string $class
     * @return ?ReflectionMethod
     */
    protected static function getConstructorParameters(string $class)
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
        $docCommentValue = self::constructorAnnotationsAsArray($docComment, '@value');
        $docCommentFaker = self::constructorAnnotationsAsArray($docComment, '@faker');

        return [
            'value' => $docCommentValue,
            'faker' => $docCommentFaker,
        ];
    }

    /**
     * Metodo para convertir un array de anotaciones en un array asociativo
     *
     * @param array $annotations
     * @return array
     */
    protected static function constructorAnnotationsAsArray(array $docComment, string $typeAnnotation)
    {
        $array = [];

        $docComment = array_map(function ($item) use ($typeAnnotation) {
            if (strpos($item, $typeAnnotation) !== false) {
                return substr(trim(str_replace([$typeAnnotation, '*'], '', $item)), 1);
            }
        }, $docComment);

        foreach ($docComment as $annotation) {
            try {
                $firstSpace = strpos($annotation, ' ');
                $key = substr($annotation, 0, $firstSpace);
                $value = substr($annotation, $firstSpace + 1);
                $array[$key] = self::getValueFromDocComment($value, $typeAnnotation);
            } catch (\Throwable $th) {
                throw new \Exception("Check the syntax of the annotation for value \"$value\" with annotation: $annotation");
            }
        }

        $array = array_filter($array, function ($item) {
            return !empty($item);
        });

        return $array;
    }

    protected static function getValueFromDocComment(string $lineComment, string $typeAnnotation)
    {
        if ($typeAnnotation === '@faker') {
            return $lineComment;
        }

        if ($typeAnnotation === '@value') {
            return eval("return $lineComment;");
        }

        throw new \Exception("Invalid annotation type \"$typeAnnotation\"");
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
