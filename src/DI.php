<?php

namespace Inilim\DI;

class DI
{
    /**
     * @var null|array<string,class-string|object>
     */
    protected static ?array $classes_swap = null;

    /**
     * @param class-string $class_name
     * @return class-string|object
     */
    public static function swap(string $class_name): string|object
    {
        return self::$classes_swap[$class_name] ?? $class_name;
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
}
