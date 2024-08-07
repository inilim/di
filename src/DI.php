<?php

namespace Inilim\DI;

use Inilim\DI\BindItem;
use Inilim\DI\PrimitiveItem;

class DI
{
    protected const PREFIX_SINGLETON = '|s';
    protected const SEP = '|';

    /**
     * @var null|array<string,class-string|object>
     */
    protected static ?array $swaps = null;
    /**
     * @var array<string,object>
     */
    protected static array $singleton = [];
    /**
     * @var null|array<string,BindItem|true>
     */
    protected static ?array $binds = null;
    /**
     * @var null|array<string,PrimitiveItem>
     */
    protected static ?array $primitive = null;

    // ------------------------------------------------------------------
    // ____
    // ------------------------------------------------------------------

    /**
     * non swap, only create
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    static function make(string $class_str, ...$args): object
    {
        return self::create($class_str, ...$args);
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param null|class-string $context controller
     * @return T
     */
    static function getOrMake(string $class_str, ?string $context = null, ...$args): object
    {
        if (self::hasSwap($class_str)) {
            return self::getFromSwap($class_str, $args);
        }
        if (self::hasBindSingleton($class_str)) {
            return self::getFromBindSingleton($class_str);
        }
        if (self::hasBind($class_str, $context)) {
            return self::getFromBind($class_str, $context, $args);
        }
        return self::create($class_str, $args);
    }

    // ------------------------------------------------------------------
    // Bind Singleton
    // ------------------------------------------------------------------

    /**
     * получить экземпляр если есть, иначе биндим и получаем | не рекомендуется использовать
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    static function getOrBindSingleton(string $class_str, ...$args): object
    {
        if (self::hasBindSingleton($class_str)) {
            return self::getOrMake($class_str);
        }

        if (!$args) {
            self::bindSingleton($class_str);
        } else {
            self::bindSingleton($class_str, static function () use ($class_str, $args) {
                return new $class_str(...$args);
            });
        };
        return self::getOrMake($class_str);
    }

    /**
     * @param class-string $abstract
     * @param \Closure|null $concrete
     * @return void
     */
    static function bindSingleton(
        string $abstract,
        ?\Closure $concrete = null,
    ): void {
        self::$binds ??= [];
        self::$binds[self::hash($abstract . self::PREFIX_SINGLETON)] = new BindItem($concrete ?? $abstract);
    }

    // ------------------------------------------------------------------
    // Bind
    // ------------------------------------------------------------------

    /**
     * @param class-string $abstract контракт или класс реализации
     * @param \Closure|class-string $concrete реализация зависимости которя будет отдана
     * @param null|class-string|class-string[] $when тот кто запрашивает зависимость, например controller
     */
    static function bind(
        string $abstract,
        \Closure|string $concrete,
        null|string|array $when = null
    ): void {
        if (!\is_array($when)) $when = [$when];
        self::$binds ??= [];
        $item = new BindItem($concrete);
        foreach ($when as $w) {
            // self::SEP важен
            self::$binds[self::hash($abstract . self::SEP . self::trim($w))] = $item;
        }
    }

    // ------------------------------------------------------------------
    // Primitive
    // ------------------------------------------------------------------

    /**
     * @return mixed
     * @param null|class-string $context
     * @param mixed $default
     */
    static function getPrimitive(string $key, ?string $context = null, $default = null)
    {
        $h              = self::hash(self::SEP . $key);
        $h_with_context = $context ? self::hash($context . self::SEP . $key) : $h;
        $r = self::$primitive[$h_with_context] ?? self::$primitive[$h] ?? null;
        if ($r !== null) return $r->value;
        return $default;
    }

    /**
     * class-string|key = context|key
     * @param mixed $give возвращаемое значение
     * @param null|class-string|class-string[] $when тот кто запрашивает, например controller
     * @return void
     */
    static function bindPrimitive(
        string $key,
        $give,
        null|string|array $when = null
    ): void {
        if (!\is_array($when)) $when = [$when];
        self::$primitive ??= [];
        $item = new PrimitiveItem($give);
        foreach ($when as $w) {
            // "|" очень важен, к примеру у нас есть класс:
            // App\Record\ArticleLongGreed
            // и есть ключ для примитива Greed
            // Но еще у нас есть класс:
            // App\Record\ArticleLong
            // если делать конкатенацию без вертикальной черты, то может получится так что мы биндим значение в некорректный класс "ArticleLongGreed"
            self::$primitive[self::hash(($w ?? '') . self::SEP . $key)] = $item;
        }
    }

    // ------------------------------------------------------------------
    // protected
    // ------------------------------------------------------------------

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @return T
     */
    protected static function getFromBindSingleton(string $class_str): object
    {
        $h = self::hash($class_str . self::PREFIX_SINGLETON);

        if (isset(self::$singleton[$h])) {
            return self::$singleton[$h];
        }

        $b = self::$binds[$h];
        /** @var BindItem $b */

        if (\is_string($b->concrete)) $obj = self::create($b->concrete, []);
        else $obj = ($b->concrete)();
        /** @var object $obj */

        // стоит ли обнулять bind?
        self::$binds[$h] = true;

        return self::$singleton[$h] = $obj;
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param null|class-string $context
     * @param mixed[]|array{} $args
     * @return T
     */
    protected static function getFromBind(string $class_str, ?string $context, array $args): object
    {
        $h              = self::hash($class_str . self::SEP);
        $h_with_context = $context ? self::hash($class_str . self::SEP . self::trim($context)) : $h;
        $b              = self::$binds[$h_with_context] ?? self::$binds[$h];
        /** @var BindItem $b */

        if (\is_string($b->concrete)) $obj = self::create($b->concrete, $args);
        else $obj = ($b->concrete)(...$args);
        /** @var object $obj */

        return $obj;
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    protected static function getFromSwap(string $class_str, array $args): object
    {
        $class_or_obj = self::$swaps[self::hash($class_str)];
        if (\is_string($class_or_obj)) {
            return self::create($class_or_obj, $args);
        }
        return $class_or_obj;
    }

    // ------------------------------------------------------------------
    // has
    // ------------------------------------------------------------------

    protected static function hasSwap(string $class_str): bool
    {
        if (self::$swaps === null) return false;
        return isset(self::$swaps[self::hash($class_str)]);
    }

    protected static function hasBindSingleton(string $class_str): bool
    {
        if (self::$binds === null) return false;
        return isset(self::$binds[self::hash($class_str . self::PREFIX_SINGLETON)]);
    }

    protected static function hasBind(string $class_str, ?string $context): bool
    {
        if (self::$binds === null) return false;

        $h              = self::hash($class_str . self::SEP);
        $h_with_context = $context ? self::hash($class_str . self::SEP . self::trim($context)) : $h;

        return isset(self::$binds[$h_with_context])
            ||
            isset(self::$binds[$h]);
    }

    // ------------------------------------------------------------------
    // ____
    // ------------------------------------------------------------------

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    protected static function create(string $class_str, array $args): object
    {
        if ($args) return new $class_str(...$args);
        return new $class_str;
    }

    protected static function hash(?string $value): string
    {
        $h = self::trim($value);
        if ($h === '') return '';
        return \md5($h);
    }

    protected static function trim(?string $value): string
    {
        return $value ? \ltrim($value, '\\') : '';
    }
}
