<?php

namespace Roke\PhpFactory\Tests\Objects;

class ComplexObjectWithDocComment extends SimpleObjectWithDocComment
{
    public SimpleObjectWithDocComment $simpleObject;

    /**
     * @param int $number
     * @value $number [1,2,3,4,5,6,7,8,9,10]
     * @value $string ["hola", "adios", "hasta luego"]
     * @value $array [[1,2,3,4],[5,6,7,8], [9,10,11,12]]
     * @value $bool [true, false]
     * @value $float [1.2, 2.3, 3.4, 4.5]
     * @value $mixed [1, "hola", true, 1.2, [1,2,3,4]]
     */
    public function __construct(int $number, string $string, array $array, bool $bool, float $float, $mixed, int $numberRandom, string $stringRandom, array $arrayRandom, bool $boolRandom, float $floatRandom, $mixedRandom, SimpleObjectWithDocComment $simpleObject)
    {
        parent::__construct($number, $string, $array, $bool, $float, $mixed, $numberRandom, $stringRandom, $arrayRandom, $boolRandom, $floatRandom, $mixedRandom);
        $this->simpleObject = $simpleObject;
    }
}