# [EN] DI (Dependency Injection) Library

This library provides a powerful and flexible dependency injection system for PHP applications. It allows easy management of dependencies, creates objects with automatic dependency injection, and configures different implementations for contracts and interfaces.

## Features

- **Simple dependency injection**: Automatic object creation with dependency injection
- **Context support**: Ability to configure different implementations based on context
- **Tag support**: Using tags for dependency identification
- **Singletons**: Singleton creation support to save resources
- **Swap functionality**: Ability to replace implementations at runtime
- **Flexible configuration**: Closure support for complex object creation logic

## Installation

```bash
composer require inilim/di
```

## Quick Start

```php
use Inilim\DI\DI;

// Object creation
$obj = \DI(MyClass::class);

// Object creation with arguments
$obj = \DI(MyClass::class, ['arg1', 'arg2']);

// Object creation with context
$obj = \DI(MyClass::class, null, MyContext::class);
```

## Dependency Binding

```php
use Inilim\DI\Bind;

$bind = Bind::self();

// Simple interface to implementation binding
$bind->class(Interface::class, Implementation::class);

// Singleton binding
$bind->singleton(Service::class);

// Binding using closure
$bind->class(Service::class, function($di, $args) {
    return new Service($di->class(Dependency::class));
});

// Binding with context
$bind->class(Interface::class, ImplementationA::class, ContextA::class);
$bind->class(Interface::class, ImplementationB::class, ContextB::class);

// Conditional binding (doesn't overwrite existing)
$bind->classIf(Interface::class, FallbackImplementation::class);

// Tag-based binding
$bind->classTag('my_tag', MyService::class);

// Conditional tag-based binding
$bind->classTagIf('optional_tag', OptionalService::class);

// Registering a list of singletons
$bind->classSingletonList([
    ServiceA::class,
    ServiceB::class,
    ServiceC::class,
]);

// Conditional singleton registration
$bind->singletonIf(ConfigService::class, ConfigService::class);

// Singleton registration by tag
$bind->singletonTag('db_connection', DatabaseConnection::class);

// Conditional singleton registration by tag
$bind->singletonTagIf('cache', CacheService::class);
```

## Using Tags

```php
// Tag-based binding
$bind->classTag('logger', FileLogger::class);

// Getting object by tag
$logger = \DITag('logger');
```

## Singletons

```php
// Singleton registration
$bind->singleton(DatabaseConnection::class);

// All calls will return the same instance
$conn1 = \DI(DatabaseConnection::class);
$conn2 = \DI(DatabaseConnection::class);
$conn1 === $conn2 // true
```

## Implementation Swapping (Swap)

```php
use Inilim\DI\Swap;

// Implementation replacement
Swap::self()->class(OriginalClass::class, MockClass::class);

// Tag-based replacement
Swap::self()->classTag('original_tag', MockClass::class);

// Value replacement
Swap::self()->value('original_tag', 'mocked_value');
```

### Removing Swaps

Swaps can be removed when they are no longer needed, for example between tests. The original bindings are restored automatically (including already resolved singletons).

```php
// Remove the swap of a specific dependency
Swap::self()->removeClass(OriginalClass::class);

// Remove the swap of a tagged dependency
Swap::self()->removeClassTag('original_tag');

// Remove the swap of a value
Swap::self()->removeValue('original_tag');

// Context-specific removal
Swap::self()->removeClass(OriginalClass::class, MyContext::class);

// Remove all swaps at once
Swap::self()->clearAll();
```

Removing a swap that doesn't exist is a silent no-op, so it's safe to call unconditionally.

## Testing with Mocks and Swaps

When writing PHPUnit tests, you can use the swap functionality to replace real implementations with mocks:

```php
use PHPUnit\Framework\TestCase;
use Inilim\DI\Bind;
use Inilim\DI\Swap;

class MyServiceTest extends TestCase
{
    public function testServiceWithMockDependency()
    {
        // Create a mock for the dependency
        $mockDependency = $this->createMock(DependencyInterface::class);
        $mockDependency->method('someMethod')
                       ->willReturn('mocked result');

        // Swap the real dependency with the mock
        Swap::self()->class(DependencyInterface::class, $mockDependency);

        // Now when we create our service, it will use the mock
        $service = \DI(MyService::class);
        
        // Perform your test
        $result = $service->doSomething();
        
        // Assertions
        $this->assertEquals('expected result', $result);
    }

    protected function tearDown(): void
    {
        // Remove all swaps after each test
        Swap::self()->clearAll();
    }
}
```

## Contextual Dependency

The library supports contextual dependency, allowing different implementations of the same interface based on the application context.

```php
// Different implementations for different contexts
$bind->class(RepositoryInterface::class, UserRepository::class, UserController::class);
$bind->class(RepositoryInterface::class, OrderRepository::class, OrderController::class);
```

## Requirements

- PHP >= 7.4

## License

MIT License



# [RU] DI (Dependency Injection) Library

Эта библиотека предоставляет мощную и гибкую систему внедрения зависимостей (Dependency Injection) для PHP-приложений. Она позволяет легко управлять зависимостями, создавать объекты с автоматическим внедрением зависимостей и настраивать различные реализации для контрактов и интерфейсов.

## Особенности

- **Простое внедрение зависимостей**: Автоматическое создание объектов с внедрением зависимостей
- **Поддержка контекстов**: Возможность настройки различных реализаций в зависимости от контекста
- **Поддержка тегов**: Использование тегов для идентификации зависимостей
- **Синглтоны**: Поддержка создания синглтонов для экономии ресурсов
- **Swap-функциональность**: Возможность замены реализаций в runtime
- **Гибкая конфигурация**: Поддержка замыканий для сложной логики создания объектов

## Установка

```bash
composer require inilim/di
```

## Быстрый старт

```php
// Создание объекта
$obj = \DI(MyClass::class);

// Создание объекта с аргументами
$obj = \DI(MyClass::class, ['arg1', 'arg2']);

// Создание объекта с контекстом
$obj = \DI(MyClass::class, null, MyContext::class);
```

## Привязка зависимостей

```php
use Inilim\DI\Bind;

$bind = Bind::self();

// Простая привязка интерфейса к реализации
$bind->class(Interface::class, Implementation::class);

// Привязка синглтона
$bind->singleton(Service::class);

// Привязка с использованием замыкания
$bind->class(Service::class, function($di, $args) {
    return new Service($di->class(Dependency::class));
});

// Привязка с контекстом
$bind->class(Interface::class, ImplementationA::class, ContextA::class);
$bind->class(Interface::class, ImplementationB::class, ContextB::class);

// Условная привязка (не перезаписывает существующую)
$bind->classIf(Interface::class, FallbackImplementation::class);

// Привязка по тегу
$bind->classTag('my_tag', MyService::class);

// Условная привязка по тегу
$bind->classTagIf('optional_tag', OptionalService::class);

// Регистрация списка синглтонов
$bind->classSingletonList([
    ServiceA::class,
    ServiceB::class,
    ServiceC::class,
]);

// Условная регистрация синглтона
$bind->singletonIf(ConfigService::class, ConfigService::class);

// Регистрация синглтона по тегу
$bind->singletonTag('db_connection', DatabaseConnection::class);

// Условная регистрация синглтона по тегу
$bind->singletonTagIf('cache', CacheService::class);
```

## Использование тегов

```php
// Привязка по тегу
$bind->classTag('logger', FileLogger::class);

// Получение объекта по тегу
$logger = \DITag('logger');
```

## Синглтоны

```php
// Регистрация синглтона
$bind->singleton(DatabaseConnection::class);

// Все вызовы будут возвращать один и тот же экземпляр
$conn1 = \DI(DatabaseConnection::class);
$conn2 = \DI(DatabaseConnection::class);
$conn1 === $conn2 // true
```

## Замена реализаций (Swap)

```php
use Inilim\DI\Swap;

// Замена реализации
Swap::self()->class(OriginalClass::class, MockClass::class);

// Замена по тегу
Swap::self()->classTag('original_tag', MockClass::class);

// Замена значения
Swap::self()->value('original_tag', 'mocked_value');
```

### Снятие подмен

Подмены можно убрать, когда они больше не нужны, например между тестами. Оригинальные привязки восстанавливаются автоматически (включая уже разрешённые синглтоны).

```php
// Убрать подмену конкретной зависимости
Swap::self()->removeClass(OriginalClass::class);

// Убрать подмену зависимости по тегу
Swap::self()->removeClassTag('original_tag');

// Убрать подмену значения
Swap::self()->removeValue('original_tag');

// Снятие подмены для конкретного контекста
Swap::self()->removeClass(OriginalClass::class, MyContext::class);

// Убрать все подмены разом
Swap::self()->clearAll();
```

Удаление несуществующей подмены — тихий no-op, поэтому вызывать можно безусловно.

## Контекстная зависимость

Библиотека поддерживает контекстную зависимость, что позволяет использовать разные реализации одного и того же интерфейса в зависимости от контекста приложения.

```php
// Разные реализации для разных контекстов
$bind->class(RepositoryInterface::class, UserRepository::class, UserController::class);
$bind->class(RepositoryInterface::class, OrderRepository::class, OrderController::class);
```

## Требования

- PHP >= 7.4

## License

MIT License