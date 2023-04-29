<?php

namespace Roke\PhpFactory\Tests;
use PHPUnit\Framework\TestCase;
use Roke\PhpFactory\DynamicFactory;
use Roke\PhpFactory\Tests\Objects\ComplexObject;
use Roke\PhpFactory\Tests\Objects\ComplexObjectWithDocComment;
use Roke\PhpFactory\Tests\Objects\OneClass;
use Roke\PhpFactory\Tests\Objects\SimpleObject;
use Roke\PhpFactory\Tests\Objects\SimpleObjectWithDocComment;

class CreateObjectTest extends TestCase
{
    public function testSimpleObjectWithDocCommentCreate()
    {
        $object1 = DynamicFactory::create(SimpleObjectWithDocComment::class);
        $this->assertInstanceOf(SimpleObjectWithDocComment::class, $object1);
        $this->assertContains($object1->number, [1,2,3,4,5,6,7,8,9,10]);
        $this->assertContains($object1->string, ["hola", "adios", "hasta luego"]);
        $this->assertContains($object1->array, [[1,2,3,4],[5,6,7,8], [9,10,11,12]]);
        $this->assertContains($object1->bool, [true, false]);
        $this->assertContains($object1->float, [1.2, 2.3, 3.4, 4.5]);
        $this->assertContains($object1->mixed, [1, "hola", true, 1.2, [1,2,3,4]]);
        $this->assertIsInt($object1->numberRandom);
        $this->assertIsString($object1->stringRandom);
        $this->assertIsArray($object1->arrayRandom);
        $this->assertIsBool( $object1->boolRandom);
        $this->assertIsFloat($object1->floatRandom);
    }


    public function testComplexObjectWithDocCommentCreate()
    {
        $object1 = DynamicFactory::create(ComplexObjectWithDocComment::class);
        $this->assertInstanceOf(ComplexObjectWithDocComment::class, $object1);
        $this->assertContains($object1->number, [1,2,3,4,5,6,7,8,9,10]);
        $this->assertContains($object1->string, ["hola", "adios", "hasta luego"]);
        $this->assertContains($object1->array, [[1,2,3,4],[5,6,7,8], [9,10,11,12]]);
        $this->assertContains($object1->bool, [true, false]);
        $this->assertContains($object1->float, [1.2, 2.3, 3.4, 4.5]);
        $this->assertContains($object1->mixed, [1, "hola", true, 1.2, [1,2,3,4]]);
        $this->assertIsInt($object1->numberRandom);
        $this->assertIsString($object1->stringRandom);
        $this->assertIsArray($object1->arrayRandom);
        $this->assertIsBool( $object1->boolRandom);
        $this->assertIsFloat($object1->floatRandom);

        $this->assertIsInt($object1->simpleObject->numberRandom);
        $this->assertIsString($object1->simpleObject->stringRandom);
        $this->assertIsArray($object1->simpleObject->arrayRandom);
        $this->assertIsBool( $object1->simpleObject->boolRandom);
        $this->assertIsFloat($object1->simpleObject->floatRandom);

        $this->assertContains($object1->simpleObject->number, [1,2,3,4,5,6,7,8,9,10]);
        $this->assertContains($object1->simpleObject->string, ["hola", "adios", "hasta luego"]);
        $this->assertContains($object1->simpleObject->array, [[1,2,3,4],[5,6,7,8], [9,10,11,12]]);
        $this->assertContains($object1->simpleObject->bool, [true, false]);
        $this->assertContains($object1->simpleObject->float, [1.2, 2.3, 3.4, 4.5]);
        $this->assertContains($object1->simpleObject->mixed, [1, "hola", true, 1.2, [1,2,3,4]]);
    }

    public function testSimpleObjectCreate()
    {
        $object1 = DynamicFactory::create(SimpleObject::class);
        $this->assertInstanceOf(SimpleObject::class, $object1);
        $this->assertIsInt($object1->numberRandom);
        $this->assertIsString($object1->stringRandom);
        $this->assertIsArray($object1->arrayRandom);
        $this->assertIsBool( $object1->boolRandom);
        $this->assertIsFloat($object1->floatRandom);
    }


    public function testComplexObjectCreate()
    {
        $object1 = DynamicFactory::create(ComplexObject::class);
        $this->assertInstanceOf(ComplexObject::class, $object1);
        $this->assertIsInt($object1->numberRandom);
        $this->assertIsString($object1->stringRandom);
        $this->assertIsArray($object1->arrayRandom);
        $this->assertIsBool( $object1->boolRandom);
        $this->assertIsFloat($object1->floatRandom);

        $this->assertInstanceOf(SimpleObject::class, $object1->simpleObject);

        $this->assertIsInt($object1->simpleObject->numberRandom);
        $this->assertIsString($object1->simpleObject->stringRandom);
        $this->assertIsArray($object1->simpleObject->arrayRandom);
        $this->assertIsBool( $object1->simpleObject->boolRandom);
        $this->assertIsFloat($object1->simpleObject->floatRandom);
    }
}