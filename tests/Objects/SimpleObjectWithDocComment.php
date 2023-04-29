<?php

namespace Roke\PhpFactory\Tests\Objects;

class SimpleObjectWithDocComment
{
    public int $number;
    public int $numberRandom;
    public string $string;
    public string $stringRandom;
    public array $array;
    public array $arrayRandom;
    public bool $bool;
    public bool $boolRandom;
    public float $float;
    public float $floatRandom;
    public $mixed;
    public $mixedRandom;

    /**
     * @param int $number
     * @value $number [1,2,3,4,5,6,7,8,9,10]
     * @value $string ["hola", "adios", "hasta luego"]
     * @value $array [[1,2,3,4],[5,6,7,8], [9,10,11,12]]
     * @value $bool [true, false]
     * @value $float [1.2, 2.3, 3.4, 4.5]
     * @value $mixed [1, "hola", true, 1.2, [1,2,3,4]]
     */
    public function __construct(int $number, string $string, array $array, bool $bool, float $float, $mixed, int $numberRandom, string $stringRandom, array $arrayRandom, bool $boolRandom, float $floatRandom, $mixedRandom)
    {
        $this->number = $number;
        $this->string = $string;
        $this->array = $array;
        $this->bool = $bool;
        $this->float = $float;
        $this->mixed = $mixed;
        $this->numberRandom = $numberRandom;
        $this->stringRandom = $stringRandom;
        $this->arrayRandom = $arrayRandom;
        $this->boolRandom = $boolRandom;
        $this->floatRandom = $floatRandom;
        $this->mixedRandom = $mixedRandom;
    }
}