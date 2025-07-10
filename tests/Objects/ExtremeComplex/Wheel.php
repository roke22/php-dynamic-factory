<?php

namespace Roke\PhpFactory\Tests\Objects\ExtremeComplex;

class Wheel
{
    public int $size;
    public string $brand;

    public function __construct(int $size, string $brand)
    {
        $this->size = $size;
        $this->brand = $brand;
    }
}
