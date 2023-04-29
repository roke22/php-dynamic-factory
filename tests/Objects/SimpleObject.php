<?php

namespace Roke\PhpFactory\Tests\Objects;

class SimpleObject
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