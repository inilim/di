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
composer require inilim/di:dev-main
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
    return new Service($di->DI(Dependency::class));
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
$bind->singletonList([
    ServiceA::class,
    ServiceB::class,
    ServiceC::class
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
// Implementation replacement
$bind->swap(OriginalClass::class, MockClass::class);

// Tag-based replacement
$bind->swapTag('original_tag', 'mock_tag');
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
composer require inilim/di:dev-main
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
    return new Service($di->DI(Dependency::class));
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
$bind->singletonList([
    ServiceA::class,
    ServiceB::class,
    ServiceC::class
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
// Замена реализации
$bind->swap(OriginalClass::class, MockClass::class);

// Замена по тегу
$bind->swapTag('original_tag', 'mock_tag');
```

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