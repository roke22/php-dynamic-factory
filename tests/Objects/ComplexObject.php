<?php

namespace Roke\PhpFactory\Tests\Objects;

class ComplexObject extends SimpleObject
{
    public SimpleObject $simpleObject;

    public function __construct(int $number, string $string, array $array, bool $bool, float $float, $mixed, int $numberRandom, string $stringRandom, array $arrayRandom, bool $boolRandom, float $floatRandom, $mixedRandom, SimpleObject $simpleObject)
    {
        parent::__construct($number, $string, $array, $bool, $float, $mixed, $numberRandom, $stringRandom, $arrayRandom, $boolRandom, $floatRandom, $mixedRandom);
        $this->simpleObject = $simpleObject;
    }
}