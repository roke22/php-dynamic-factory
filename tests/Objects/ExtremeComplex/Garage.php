<?php

namespace Roke\PhpFactory\Tests\Objects\ExtremeComplex;

class Garage
{
    public string $location;
    public Car $mainCar;
    public ?string $ownerName;

    public function __construct(string $location, Car $mainCar, ?string $ownerName = 'Default Owner')
    {
        $this->location = $location;
        $this->mainCar = $mainCar;
        $this->ownerName = $ownerName;
    }
}
