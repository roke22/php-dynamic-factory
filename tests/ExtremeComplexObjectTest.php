<?php

namespace Roke\PhpFactory\Tests;

use PHPUnit\Framework\TestCase;
use Roke\PhpFactory\ArrayToObject;
use Roke\PhpFactory\Tests\Objects\ExtremeComplex\Car;
use Roke\PhpFactory\Tests\Objects\ExtremeComplex\DataSource;
use Roke\PhpFactory\Tests\Objects\ExtremeComplex\Engine;
use Roke\PhpFactory\Tests\Objects\ExtremeComplex\Garage;
use Roke\PhpFactory\Tests\Objects\ExtremeComplex\Report;
use Roke\PhpFactory\Tests\Objects\ExtremeComplex\Wheel;

class ExtremeComplexObjectTest extends TestCase
{
    /**
     * Test 1A: Frankenstein with snake_case (default behavior)
     */
    public function testFrankensteinObjectCreationWithSnakeCaseDefault()
    {
        $data = [
            'location' => 'Main Base',
            'main_car' => [
                'model_name' => 'Z-2000',
                'engine' => [
                    'liters' => 6.2,
                    'cylinders' => 8,
                ],
                'wheels' => [
                    ['size' => 18, 'brand' => 'BrandA'],
                    ['size' => 18, 'brand' => 'BrandB'],
                ],
            ],
            'owner_name' => 'Dr. Frankenstein',
        ];

        // Relying on the default snake_case conversion
        $garage = ArrayToObject::make($data, Garage::class);

        $this->assertInstanceOf(Garage::class, $garage);
        $this->assertEquals('Dr. Frankenstein', $garage->ownerName);
        $this->assertInstanceOf(Car::class, $garage->mainCar);
        $this->assertEquals('Z-2000', $garage->mainCar->modelName);
        $this->assertInstanceOf(Engine::class, $garage->mainCar->engine);
        $this->assertEquals(8, $garage->mainCar->engine->cylinders);
        $this->assertCount(2, $garage->mainCar->wheels);
        $this->assertInstanceOf(Wheel::class, $garage->mainCar->wheels[0]);
    }

    /**
     * Test 1B: Frankenstein with camelCase (explicitly stated)
     */
    public function testFrankensteinObjectCreationWithCamelCaseExplicit()
    {
        $data = [
            'location' => 'Main Base',
            'mainCar' => [ // camelCase key
                'modelName' => 'Z-2000',
                'engine' => [
                    'liters' => 6.2,
                    'cylinders' => 8,
                ],
                'wheels' => [
                    ['size' => 18, 'brand' => 'BrandA'],
                    ['size' => 18, 'brand' => 'BrandB'],
                ],
            ],
            'ownerName' => 'Dr. Frankenstein', // camelCase key
        ];

        // Explicitly stating the input is camelCase
        $garage = ArrayToObject::make($data, Garage::class, ArrayToObject::CAMEL);

        $this->assertInstanceOf(Garage::class, $garage);
        $this->assertEquals('Dr. Frankenstein', $garage->ownerName);
        $this->assertInstanceOf(Car::class, $garage->mainCar);
        $this->assertEquals('Z-2000', $garage->mainCar->modelName);
        $this->assertInstanceOf(Engine::class, $garage->mainCar->engine);
        $this->assertEquals(8, $garage->mainCar->engine->cylinders);
        $this->assertCount(2, $garage->mainCar->wheels);
        $this->assertInstanceOf(Wheel::class, $garage->mainCar->wheels[0]);
    }


    /**
     * Test 2: The "Null and Defaults Nightmare"
     */
    public function testNullAndDefaultsNightmare()
    {
        // Case 1: Provide the bare minimum. Expect defaults and nulls to be set correctly.
        $data1 = [
            'report_id' => 'report-123',
            'source' => null,
        ];

        $report1 = ArrayToObject::make($data1, Report::class);

        $this->assertInstanceOf(Report::class, $report1);
        $this->assertEquals('report-123', $report1->reportId);
        $this->assertNull($report1->source);
        $this->assertEquals([], $report1->data);
        $this->assertEquals('PENDING', $report1->status);
        $this->assertNull($report1->errorMessage);

        // Case 2: Provide some optional values, but not all.
        $data2 = [
            'report_id' => 'report-456',
            'source' => [
                'source_name' => 'API Feed'
            ],
            'status' => 'COMPLETED',
        ];

        $report2 = ArrayToObject::make($data2, Report::class);

        $this->assertInstanceOf(Report::class, $report2);
        $this->assertEquals('report-456', $report2->reportId);
        $this->assertEquals('COMPLETED', $report2->status);
        $this->assertNull($report2->errorMessage);
        $this->assertInstanceOf(DataSource::class, $report2->source);
        $this->assertEquals('API Feed', $report2->source->sourceName);
    }
}
