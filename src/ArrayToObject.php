<?php

namespace Roke\PhpFactory;

use ReflectionClass;
use Roke\PhpFactory\Support\annotationType;
use Roke\PhpFactory\Support\ClasesStyles;
use Roke\PhpFactory\Support\ReflectionMethod;

class ArrayToObject
{
    public CONST SNAKE = "snake";
    public CONST CAMEL = "camel";
    public CONST STUDLY = "studly";
    public CONST ALL = "all";
    public CONST NONE = "none";

    /**
     * Metodo publico para llamar a la utilidad
     *
     * @param array $array Array data to create the object
     * @param string $class Name of the object to create with data in array param
     * @param annotationType $string Annotation type you want to use to transform your array data to class construct params
     * @return mixed Return the class of string name constructed
     */
    public static function make(array $array, string $class, string $annotationType = "all")
    {
        $constructor = self::getConstructorParameters($class);

        if ($annotationType === self::ALL) {
            $array = ClasesStyles::allCases($array);
        }

        if ($annotationType === self::SNAKE) {
            $array = ClasesStyles::allCases($array);
        }

        if ($annotationType === self::CAMEL) {
            $array = ClasesStyles::allCases($array);
        }

        if ($annotationType === self::STUDLY) {
            $array = ClasesStyles::allCases($array);
        }

        $dataConstructor = self::createObject($array, $constructor);

        //Creamos la clase ...$dataConstructor hace que cada elemento del array sea un parametro a pasar
        return new $class(...$dataConstructor);
    }

    /**
     * Metodo para obtener un array de los parametros que tiene el constructor de la clase
     *
     * @param string $class
     * @return ?ReflectionMethod
     */
    protected static function getConstructorParameters(string $class)
    {
        $reflection = new ReflectionClass($class);
        return $reflection->getConstructor();
    }

    /**
     * Metodo para obtener las anotaiones del constructor y obtener solo la linea del parametro que buscamos
     * Su anotacion debe ser:
     * array<Namespace\De\Tu\Clase>
     *
     * @param [type] $constructor
     * @param string $param
     * @return void
     */
    protected static function getDocCommentByParam($constructor, string $param)
    {
        $doc = $constructor->getDocComment();

        if ($doc === false) {
            return null;
        }

        $doc = explode(PHP_EOL, $doc);

        foreach ($doc as $line) {
            if (strpos($line, '$'.$param) && strpos($line, '<') && strpos($line, '>')) {
                return substr($line, (strpos($line, '<')+1), strpos($line, '>') - (strpos($line, '<')+1));
            }
        }

        return null;
    }

    /**
     * Este metodo es el que compone el array con los parametros que se debe pasar al constructor
     *
     * @param array $data
     * @param [type] $constructor
     * @return void
     */
    protected static function createObject(array $data, $constructor)
    {
        $dataConstructor = [];
        //Para cada parametro del constructor hacemos una iteracion
        foreach ($constructor->getParameters() as $parameter) {
            //Si el parametro es un tipo primitivo añadimos al array su valor

            if ($parameter->getType() === null){
                $dataConstructor[] = (isset($data[$parameter->name])) ? $data[$parameter->name] : null;
            }

            if (($parameter->getType() !== null) && (in_array($parameter->getType()->getName(), ['string', 'int', 'bool', 'float']) === true)) {
                $dataConstructor[] = (isset($data[$parameter->name])) ? $data[$parameter->name] : null;
            }

            //Si es de tipo array debemos saber si es un array basico o bien un array de clases a traves de las anotaciones
            if (($parameter->getType() !== null) && in_array($parameter->getType()->getName(), ['array']) === true) {
                //Obtenemos por la anotacion la clase que tiene este array
                $sourceClass = self::getDocCommentByParam($constructor, $parameter->name);

                //En caso de un array compuesto por elementos de otra clase creamos estas clases y las metemos en un array
                if ($sourceClass != null) {
                    $dependencies = [];

                    //Creamos todos los elementos
                    foreach ($data as $item) {
                        $dependencies[] = self::make($item, $sourceClass);
                    }
                    //Una vez tenemos todos los elementos del array creados los añadimos a los parametros que le pasaremos al constructor
                    $dataConstructor[] = $dependencies;
                }

                //Si no tiene anotacion de clase es que es un array normal y lo añadimos a los parametros que le pasaremos al constructor
                if ($sourceClass == null) {
                    $dataConstructor[] = (isset($data[$parameter->name])) ? $data[$parameter->name] : null;
                }
            }

            //En caso de no ser un tipo primitivo es una clase y por lo tanto lo que hacemos por recursividad es crear la clase pasando todos sus parametros
            if (($parameter->getType() !== null) && in_array($parameter->getType()->getName(), ['string', 'int', 'bool', 'float', 'array']) === false) {
                $dataIteration = (isset($data[$parameter->name])) ? $data[$parameter->name] : null;
                $dataConstructor[] = self::make($dataIteration, $parameter->getType()->getName());
            }
        }

        return $dataConstructor;
    }
}