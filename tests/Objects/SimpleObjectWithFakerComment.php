<?php

namespace Roke\PhpFactory\Tests\Objects;

class SimpleObjectWithFakerComment
{
    public int $number;
    public string $string;
    public array $array;
    public bool $bool;
    public float $float;

    /**
     * @faker $number $faker->numberBetween(0, 100)
     * @faker $string $faker->word
     * @faker $array $faker->words(3)
     * @faker $bool $faker->boolean
     * @faker $float $faker->randomFloat(2, 0, 100)
     */
    public function __construct($number, $string, $array, $bool, $float)
    {
        $this->number = $number;
        $this->string = $string;
        $this->array = $array;
        $this->bool = $bool;
        $this->float = $float;
    }
}