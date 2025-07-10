# PHP Dynamic Factory

[![PHP Tests](https://github.com/roke22/php-dynamic-factory/actions/workflows/php.yml/badge.svg)](https://github.com/roke22/php-dynamic-factory/actions/workflows/php.yml)

A simple and powerful PHP library to dynamically create objects from arrays. It intelligently maps array keys to your class constructor parameters, saving you from writing repetitive boilerplate code.

Its main strengths are:
-   Automatic `snake_case` to `camelCase` conversion, ideal for creating objects from database records or API responses.
-   Recursive creation of nested objects and arrays of objects.
-   A `DynamicFactory` capable of generating objects with random data for testing (using Faker).

## Installation

Install the library via Composer:

```bash
composer require roke22/php-dynamic-factory
```

## `ArrayToObject`: Creating Objects from Arrays

This is the core of the library. It allows you to build an object by providing its class name and an array of data.

### Basic Usage (Default `snake_case`)

By default, the library assumes your input array keys are in `snake_case` and converts them to the `camelCase` expected by your constructor parameters. This is the most common use case.

**Example:**

Let's say you have a `User` class:

```php
class User
{
    public string $userName;
    public string $userEmail;

    public function __construct(string $userName, string $userEmail)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }
}
```

And you have data from a database or an API:

```php
$userData = [
    'user_name' => 'John Doe',
    'user_email' => 'john.doe@example.com'
];
```

You can create the `User` object with a single line:

```php
use Roke\PhpFactory\ArrayToObject;

$user = ArrayToObject::make($userData, User::class);

// $user is now a User object with properties:
// $user->userName = 'John Doe';
// $user->userEmail = 'john.doe@example.com';
```

### Handling `camelCase` Input

If your input data is already in `camelCase`, you must specify it explicitly.

```php
use Roke\PhpFactory\ArrayToObject;

$userData = [
    'userName' => 'Jane Doe',
    'userEmail' => 'jane.doe@example.com'
];

// Explicitly state the input format
$user = ArrayToObject::make($userData, User::class, ArrayToObject::CAMEL);
```

### Advanced Usage: Nested Objects

The library automatically handles complex, nested objects and even arrays of objects. It reads your constructor's type hints and `@param` annotations to resolve dependencies.

**Example:**

Consider this complex structure:

```php
// Garage.php
class Garage {
    public function __construct(string $location, Car $mainCar) { /* ... */ }
}

// Car.php
class Car {
    /** @param Wheel[] $wheels */
    public function __construct(string $modelName, Engine $engine, array $wheels) { /* ... */ }
}

// Engine.php
class Engine {
    public function __construct(float $liters, int $cylinders) { /* ... */ }
}

// Wheel.php
class Wheel {
    public function __construct(int $size, string $brand) { /* ... */ }
}
```
*Note: For arrays of objects, you must use the fully qualified class name in the `@param` annotation, e.g., `@param array<\Your\Namespace\Wheel> $wheels`.*

You can build the entire `Garage` object from a single nested array:

```php
$garageData = [
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
];

$garage = ArrayToObject::make($garageData, Garage::class);

// $garage is now a fully hydrated object with a Car, which has an Engine and an array of Wheels.
```

## `DynamicFactory`: Generating Test Data

For testing, you can instantly create objects with random data powered by [Faker](https://github.com/fakerphp/faker).

### Basic Usage

```php
use Roke\PhpFactory\DynamicFactory;

// Creates a User object with random name and email
$randomUser = DynamicFactory::create(User::class);
```

### Overriding Attributes

You can provide specific values for some fields while letting the factory generate the rest.

```php
$userWithSpecificName = DynamicFactory::create(User::class, [
    'userName' => 'A specific name'
]);

// $userWithSpecificName->userName will be 'A specific name'
// $userWithSpecificName->userEmail will be a random email
```

### Advanced Faker and Value Annotations

You can control the data generation process using `@faker` and `@value` annotations in your class constructor's DocComment.

```php
class Product
{
    public string $name;
    public string $category;
    public int $price;

    /**
     * @value $category ["electronics", "books", "home"]
     * @faker $price $faker->numberBetween(100, 1000)
     */
    public function __construct(string $name, string $category, int $price)
    {
        // ...
    }
}

$product = DynamicFactory::create(Product::class);

// $product->category will be one of the specified values.
// $product->price will be a random number between 100 and 1000.
```