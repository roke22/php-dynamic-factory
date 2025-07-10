<?php

namespace Roke\PhpFactory\Tests\Objects\ExtremeComplex;

class Car
{
    public string $modelName;
    public Engine $engine;
    /** @var Wheel[] */
    public array $wheels;

    /**
     * @param string $modelName
     * @param Engine $engine
     * @param array<\Roke\PhpFactory\Tests\Objects\ExtremeComplex\Wheel> $wheels
     */
    public function __construct(string $modelName, Engine $engine, array $wheels)
    {
        $this->modelName = $modelName;
        $this->engine = $engine;
        $this->wheels = $wheels;
    }
}
