<?php

namespace Inilim\DI;

class DI
{
    /**
     * @var null|array<string,class-string|object>
     */
    protected static ?array $classes_swap = null;
    /**
     * @var array<string,object>
     */
    protected static array $singleton = [];

    public static function hasInstance(string $class_str): bool
    {
        return isset(self::$singleton[\md5($class_str)]);
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @return T
     */
    public static function getInstance(string $class_str, ...$args): object
    {
        $hash = \md5($class_str);
        if (self::$classes_swap === null) {
            self::$singleton[$hash] ??= self::make($class_str, ...$args);
            return self::$singleton[$hash];
        }

        // ------------------------------------------------------------------
        // ___
        // ------------------------------------------------------------------

        $instance = self::$singleton[$hash] ?? null;
        $t = self::make($class_str, ...$args);
        if ($instance !== null) {
            if ($instance instanceof $t) return $instance;
        }
        return self::$singleton[$hash] = $t;
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @return T
     */
    public static function make(string $class_str, ...$args): object
    {
        if (self::$classes_swap === null) {
            if ($args) return new $class_str(...$args);
            return new $class_str;
        }

        // ------------------------------------------------------------------
        // ___
        // ------------------------------------------------------------------

        $class_or_obj = self::swap($class_str);
        if (\is_string($class_or_obj)) {
            if ($args) return new $class_or_obj(...$args);
            return new $class_or_obj;
        }
        return $class_or_obj;
    }



    /**
     * @param class-string $class_str что менем
     * @param class-string|object $class_swap на что меняем
     */
    public static function addSwap(string $class_str, string|object $class_swap): void
    {
        $hash = \md5($class_str);
        self::$classes_swap ??= [];
        self::$classes_swap[$hash] = $class_swap;
    }

    // ------------------------------------------------------------------
    // protected
    // ------------------------------------------------------------------

    /**
     * @param class-string $class_str
     * @return class-string|object
     */
    protected static function swap(string $class_str): string|object
    {
        $hash = \md5($class_str);
        return self::$classes_swap[$hash] ?? $class_str;
    }
}
