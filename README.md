<p align="center">
<a href="https://github.com/roke22/php-dynamic-factory/actions"><img src="https://github.com/roke22/php-dynamic-factory/actions/workflows/php.yml/badge.svg?branch=master" alt="Build Status"></a>
</p>

# DynamicFactory

DynamicFactory is a PHP class that allows you to create objects with random or pre-defined data. This class uses the Faker library to generate random values and the ReflectionClass to analyze the constructors of the target class.

You can use annotations to force the values you want to use in the constructor of the target class. Using "@value" or "@faker" in the doc comment.

## Installation

Use the package manager [composer](https://getcomposer.org/) to install DynamicFactory.

```bash
composer require roke22/dynamic-factory
```

## Basic Usage

```php
$object = DynamicFactory::create(Object::class);
```

Where Object is the class you want to instantiate. You need to type the parameters of the constructor of the target class.   

For example:
    
```php
class OneClass {
    public $name;
    public $age;
    public $email;
    
    public function __construct(string $name, int $age, string $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
}
```

The result of the previous code will be an instance of Object with random values for each property. For example:

```
Roke\PhpFactory\Tests\Objects\OneClass)#121 (3) {
  ["name"]=>
  string(8) "repellat"
  ["age"]=>
  int(6)
  ["email"]=>
  string(9) "quibusdam"
}
```

## Using custom values
You can also specify the values you want to use in a doc comment using the annotation @value.   
You must use the "@value" follwing with the name of the parameter and the values you want to use in array format.
For example:

```
@value $nameOfValue [2,3,4,5]
```

Let's force some values to the previous example:

```php
class OneClass {
    public $name;
    public $age;
    public $email;
    
    /**
     * @value $name ['Rachel', 'Mario', 'John', 'Peter']
     * @value $age [22, 36, 45]
     * @value $email ['my@email.com', 'email@yahoo.com', 'support@company.com']
     */
    public function __construct(string $name, int $age, string $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
}
```

The DynamicFactory will use these values to create the object. For example:

```
Roke\PhpFactory\Tests\Objects\OneClass)#121 (3) {
  ["name"]=>
  string(4) "John"
  ["age"]=>
  int(36)
  ["email"]=>
  string(19) "support@company.com"
}
```

For a non typed parameter the DynamicFactory will use the default value if it exists. If not, it will use a random value of a random type.

## Using Faker values

Let's force some values using Faker (https://fakerphp.github.io/):

```php
class OneClass {
    public $name;
    public $age;
    public $email;
    
    /**
     * @faker $name $faker->name
     * @value $age $faker->randomBetween(20, 50)
     * @value $email $faker->email
     */
    public function __construct(string $name, int $age, string $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
}
```

The DynamicFactory will use the faker methods to create the object. For example:

```
Roke\PhpFactory\Tests\Objects\OneClass)#359 (3) {
  ["name"]=>
  string(20) "Willow Romaguera PhD"
  ["age"]=>
  int(43)
  ["email"]=>
  string(26) "ethel.konopelski@gmail.com"
}
```

## LICENSE
MIT License

Copyright (c) [year] [fullname]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.


