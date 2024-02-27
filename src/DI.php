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
     * @param class-string<T> $class_name
     * @return T
     */
    public static function getInstance(string $class_name, ...$args): object
    {
        if ($args) {
            self::$singleton[$class_name] ??= self::make($class_name, ...$args);
            return self::$singleton[$class_name];
        }

        // ------------------------------------------------------------------
        // ___
        // ------------------------------------------------------------------

        self::$singleton[$class_name] ??= self::make($class_name);
        return self::$singleton[$class_name];
    }

    /**
     * @template T of object
     * @param class-string<T> $class_name
     * @return T
     */
    public static function make(string $class_name, ...$args): object
    {
        $name_or_obj = self::swap($class_name);
        if (\is_string($name_or_obj)) {
            if ($args) return new $name_or_obj(...$args);
            return new $name_or_obj;
        }
        return $name_or_obj;
    }



    /**
     * @param class-string $class_name что менем
     * @param class-string|object $class_swap на что меняем
     */
    public static function setDepsSwap(string $class_name, string|object $class_swap): void
    {
        self::$classes_swap ??= [];
        self::$classes_swap[$class_name] = $class_swap;
    }

    // ------------------------------------------------------------------
    // protected
    // ------------------------------------------------------------------

    /**
     * @param class-string $class_name
     * @return class-string|object
     */
    protected static function swap(string $class_name): string|object
    {
        return self::$classes_swap[$class_name] ?? $class_name;
    }
}
