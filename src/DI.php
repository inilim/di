<?php

namespace Inilim\DI;

class DI
{
    /**
     * @var null|array<string,class-string|object>
     */
    protected static ?array $classes_swap = null;
    /**
     * @var array<class-string,object>
     */
    protected static array $singleton = [];

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @return T
     */
    public static function getInstance(string $class_str, ...$args): object
    {
        if (self::$classes_swap === null) {
            self::$singleton[$class_str] ??= self::make($class_str, ...$args);
            return self::$singleton[$class_str];
        }

        // ------------------------------------------------------------------
        // ___
        // ------------------------------------------------------------------

        $instance = self::$singleton[$class_str] ?? null;
        $t = self::make($class_str, ...$args);
        if (isset($instance)) {
            if ($instance instanceof $t) return $instance;
        }
        return self::$singleton[$class_str] = $t;
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
        self::$classes_swap ??= [];
        self::$classes_swap[$class_str] = $class_swap;
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
        return self::$classes_swap[$class_str] ?? $class_str;
    }
}
