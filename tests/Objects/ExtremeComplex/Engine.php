<?php

namespace Roke\PhpFactory\Tests\Objects\ExtremeComplex;

class Engine
{
    public float $liters;
    public int $cylinders;

    public function __construct(float $liters, int $cylinders)
    {
        $this->liters = $liters;
        $this->cylinders = $cylinders;
    }
}
