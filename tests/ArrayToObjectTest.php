<?php

namespace Roke\PhpFactory\Tests;

use PHPUnit\Framework\TestCase;
use Roke\PhpFactory\ArrayToObject;
use Roke\PhpFactory\Tests\Objects\ComplexObject;
use Roke\PhpFactory\Tests\Objects\Member;
use Roke\PhpFactory\Tests\Objects\ObjectWithDefaults;
use Roke\PhpFactory\Tests\Objects\ObjectWithNoConstructor;
use Roke\PhpFactory\Tests\Objects\SimpleObject;
use Roke\PhpFactory\Tests\Objects\Team;

class ArrayToObjectTest extends TestCase
{
    private function getCompleteSimpleObjectData(array $overrides = []): array
    {
        $base = [
            'number' => 1,
            'string' => 'base',
            'array' => [],
            'bool' => false,
            'float' => 1.0,
            'mixed' => null,
            'numberRandom' => 1,
            'stringRandom' => 'base',
            'arrayRandom' => [],
            'boolRandom' => false,
            'floatRandom' => 1.0,
            'mixedRandom' => null,
        ];

        return array_merge($base, $overrides);
    }

    public function testCreateSimpleObjectWithCamelCase()
    {
        $data = $this->getCompleteSimpleObjectData([
            'number' => 10,
            'string' => 'hello',
        ]);

        // Must now explicitly state the input is camelCase
        $object = ArrayToObject::make($data, SimpleObject::class, ArrayToObject::CAMEL);

        $this->assertInstanceOf(SimpleObject::class, $object);
        $this->assertEquals(10, $object->number);
        $this->assertEquals('hello', $object->string);
    }

    public function testCreateFromSnakeCaseWithDefaultSetting()
    {
        $data = $this->getCompleteSimpleObjectData([
            'number_random' => 99,
            'string_random' => 'snake',
        ]);

        // No case style needed, snake_case is the new default
        $object = ArrayToObject::make($data, SimpleObject::class);

        $this->assertInstanceOf(SimpleObject::class, $object);
        $this->assertEquals(99, $object->numberRandom);
        $this->assertEquals('snake', $object->stringRandom);
    }

    public function testCreateFromStudlyCase()
    {
        $data = $this->getCompleteSimpleObjectData([
            'NumberRandom' => 101,
            'StringRandom' => 'studly',
        ]);

        $object = ArrayToObject::make($data, SimpleObject::class, ArrayToObject::STUDLY);

        $this->assertInstanceOf(SimpleObject::class, $object);
        $this->assertEquals(101, $object->numberRandom);
        $this->assertEquals('studly', $object->stringRandom);
    }

    public function testCreateWithExactCaseUsingNone()
    {
        $data = $this->getCompleteSimpleObjectData(['stringRandom' => 'exact']);
        $wrongData = $this->getCompleteSimpleObjectData(['string_random' => 'not-gonna-work']);

        $object = ArrayToObject::make($data, SimpleObject::class, ArrayToObject::NONE);
        $this->assertEquals('exact', $object->stringRandom);

        $object2 = ArrayToObject::make($wrongData, SimpleObject::class, ArrayToObject::NONE);
        $this->assertNotEquals('not-gonna-work', $object2->stringRandom);
        $this->assertEquals('base', $object2->stringRandom);
    }

    public function testCreateObjectWithDependencies()
    {
        $data = [
            'number' => 1,
            'string' => 'complex',
            'array' => [], 'bool' => false, 'float' => 1.0, 'mixed' => null,
            'numberRandom' => 1, 'stringRandom' => 'base', 'arrayRandom' => [],
            'boolRandom' => false, 'floatRandom' => 1.0, 'mixedRandom' => null,
            'simpleObject' => $this->getCompleteSimpleObjectData(['string' => 'simple'])
        ];

        $object = ArrayToObject::make($data, ComplexObject::class, ArrayToObject::CAMEL);

        $this->assertInstanceOf(ComplexObject::class, $object);
        $this->assertInstanceOf(SimpleObject::class, $object->simpleObject);
        $this->assertEquals('complex', $object->string);
        $this->assertEquals('simple', $object->simpleObject->string);
    }

    public function testCreateObjectWithArrayOfObjects()
    {
        $data = [
            'team_name' => 'Coders',
            'members' => [
                ['name' => 'John Doe', 'role' => 'Developer'],
                ['name' => 'Jane Doe', 'role' => 'Designer'],
            ]
        ];

        // Use the default (snake_case) and let it convert team_name -> teamName
        $team = ArrayToObject::make($data, Team::class);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('Coders', $team->teamName);
        $this->assertCount(2, $team->members);
        $this->assertInstanceOf(Member::class, $team->members[0]);
        $this->assertEquals('John Doe', $team->members[0]->name);
    }

    public function testCreateObjectWithDefaultValues()
    {
        $data = ['required' => 'test'];
        $object = ArrayToObject::make($data, ObjectWithDefaults::class, ArrayToObject::CAMEL);

        $this->assertInstanceOf(ObjectWithDefaults::class, $object);
        $this->assertEquals('test', $object->required);
        $this->assertEquals('default', $object->optional);
        $this->assertNull($object->nullable);
    }

    public function testCreateObjectWithProvidedNullableAndOptional()
    {
        $data = [
            'required' => 'test',
            'optional' => 'not-default',
            'nullable' => 'not-null'
        ];
        $object = ArrayToObject::make($data, ObjectWithDefaults::class, ArrayToObject::CAMEL);

        $this->assertEquals('not-default', $object->optional);
        $this->assertEquals('not-null', $object->nullable);
    }

    public function testCreateClassWithNoConstructor()
    {
        $object = ArrayToObject::make([], ObjectWithNoConstructor::class);
        $this->assertInstanceOf(ObjectWithNoConstructor::class, $object);
        $this->assertEquals('bar', $object->foo);
    }
}
