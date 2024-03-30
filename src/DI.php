<?php

namespace Inilim\DI;

use Inilim\DI\BindItem;

class DI
{
    protected const POSTFIX = '|s';

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
    protected static ?array $binds  = null;

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param null|class-string $context controller
     * @return T
     */
    public static function getOrMake(string $class_str, ?string $context = null, ...$args)
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
        return self::make($class_str, $args);
    }

    /**
     * получить экземпляр если есть, иначе биндим и получаем
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    public static function getOrBindSingleton(string $class_str, ...$args)
    {
        if (self::hasBindSingleton($class_str)) {
            return self::getFromBindSingleton($class_str);
        }

        if (!$args) {
            self::bindSingleton($class_str);
        } else {
            self::bindSingleton($class_str, function () use ($class_str, $args) {
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
    public static function bindSingleton(
        string $abstract,
        ?\Closure $concrete = null,
    ): void {
        self::$binds ??= [];
        self::$binds[self::hash($abstract . self::POSTFIX)] = new BindItem($concrete ?? $abstract);
    }

    /**
     * @param class-string $abstract контракт или класс реализации
     * @param \Closure|class-string $concrete реализация зависимости которя будет отдана
     * @param null|class-string|class-string[] $when тот кто запрашивает зависимость, чаще всего controller
     */
    public static function bind(
        string $abstract,
        \Closure|string $concrete,
        null|string|array $when = null
    ): void {
        if (!\is_array($when)) $when = [$when];
        self::$binds ??= [];
        foreach ($when as $w) {
            self::$binds[self::hash($abstract . ($w ?? ''))] = new BindItem($concrete);
        }
    }

    // ------------------------------------------------------------------
    // protected
    // ------------------------------------------------------------------

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    protected static function make(string $class_str, array $args)
    {
        if ($args) return new $class_str(...$args);
        return new $class_str;
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @return T
     */
    protected static function getFromBindSingleton(string $class_str): object
    {
        $h = self::hash($class_str . self::POSTFIX);

        if (isset(self::$singleton[$h])) {
            return self::$singleton[$h];
        }

        $b = self::$binds[$h];

        if (\is_string($b->concrete)) $obj = self::make($b->concrete, []);
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
        $h              = self::hash($class_str);
        $h_with_context = self::hash($class_str . ($context ?? ''));
        $b              = self::$binds[$h_with_context] ?? self::$binds[$h];

        if (\is_string($b->concrete)) $obj = self::make($b->concrete, $args);
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
            return self::make($class_or_obj, $args);
        }
        return $class_or_obj;
    }

    protected static function hasSwap(string $class_str): bool
    {
        if (self::$swaps === null) return false;
        return isset(self::$swaps[self::hash($class_str)]);
    }

    protected static function hasBindSingleton(string $class_str): bool
    {
        if (self::$binds === null) return false;
        return isset(self::$binds[self::hash($class_str . self::POSTFIX)]);
    }

    protected static function hasBind(string $class_str, ?string $context): bool
    {
        if (self::$binds === null) return false;
        $hash              = self::hash($class_str);
        $hash_with_context = self::hash($class_str . ($context ?? ''));
        return isset(self::$binds[$hash_with_context])
            ||
            isset(self::$binds[$hash]);
    }

    protected static function hash(?string $value): string
    {
        $c = \ltrim(($value ?? ''), '\\');
        if ($c === '') return '';
        return \md5($c);
    }
}
