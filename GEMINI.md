# Guía del Proyecto: PHP Dynamic Factory

## 1. Objetivo del Proyecto

El objetivo principal de `php-dynamic-factory` es simplificar y automatizar la creación de objetos PHP a partir de arrays de datos. La biblioteca permite instanciar clases dinámicamente, mapeando los datos de un array a los parámetros del constructor de la clase.

Una característica clave es la capacidad de transformar automáticamente las claves del array de `snake_case` a `camelCase` (y viceversa), lo que elimina la necesidad de modificar manualmente el código de creación de objetos cada vez que se actualiza el constructor de una clase. Esto es especialmente útil en escenarios donde las fuentes de datos (por ejemplo, bases de datos o APIs) tienen una convención de nomenclatura diferente a la del código PHP.

Además, el proyecto incluye una factoría (`DynamicFactory`) que puede generar objetos con datos de prueba aleatorios (usando Faker), facilitando la creación de tests unitarios y de integración.

## 2. Cómo Funciona

La biblioteca utiliza principalmente la **reflexión** de PHP para analizar los constructores de las clases.

1.  **`ArrayToObject`**:
    *   Inspecciona el constructor de la clase de destino para obtener la lista de sus parámetros (nombre, tipo, etc.).
    *   Mapea los valores del array de entrada con los parámetros del constructor por su nombre.
    *   Soporta la conversión de claves entre `snake_case`, `camelCase` y `StudlyCase`.
    *   Puede manejar dependencias (otros objetos) de forma recursiva. Si un parámetro del constructor es otra clase, intentará crear una instancia de esa clase utilizando los datos del array.
    *   Puede manejar arrays de objetos complejos utilizando anotaciones `@param` en el DocComment del constructor (ej. `@param array<Namespace\MiClase> $miembros`).

2.  **`DynamicFactory`**:
    *   Se apoya en `ArrayToObject` para la construcción final del objeto.
    *   Utiliza la reflexión para leer las anotaciones del DocComment del constructor y generar datos aleatorios apropiados para cada parámetro.
    *   Permite especificar valores concretos para ciertos campos mientras que el resto se autogenera.
    *   Soporta anotaciones personalizadas como `@value` para proporcionar un conjunto de posibles valores y `@faker` para especificar un método de la librería Faker.

3.  **`Support\Str`**:
    *   Clase de utilidad para realizar las conversiones de formato de strings (`snake`, `camel`, `studly`).

## 3. Estilo de Código y Reglas

*   **Convención de Nomenclatura**:
    *   **Clases**: `StudlyCase` (ej. `DynamicFactory`).
    *   **Métodos**: `camelCase` (ej. `createObject`).
    *   **Variables y Propiedades**: `camelCase` (ej. `$arrayValues`).
    *   **Claves de Arrays (Entrada)**: Se aceptan `snake_case`, `camelCase` o `StudlyCase` y se normalizan según sea necesario.
*   **Estándares de Código**: El código sigue el estándar **PSR-12**.
*   **Documentación**: Se utiliza **PHPDoc** para documentar clases, métodos y propiedades. Los comentarios deben ser claros y describir el propósito del código.
*   **Tipado**: Se utiliza el tipado estricto de PHP 7.4+ siempre que es posible (`declare(strict_types=1);` no está presente, pero los tipos se declaran en los parámetros y retornos de las funciones).
*   **Tests**: Las pruebas se escriben con **PHPUnit**. Cada funcionalidad principal debe tener su correspondiente prueba unitaria.
*   **Dependencias**: Las dependencias se gestionan con **Composer**. La librería Faker se utiliza para la generación de datos de prueba.
