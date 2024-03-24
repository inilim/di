<?php

namespace Inilim\DI;

use Inilim\DI\DI;

class DITest extends DI
{
    /**
     * @param class-string $class_str что менем ($needs)
     * @param class-string|object $class_swap на что меняем
     */
    public static function addSwap(string $class_str, string|object $class_swap): void
    {
        self::$swaps ??= [];
        self::$swaps[self::hash($class_str)] = $class_swap;
    }

    public static function clearAllSwap(): void
    {
        self::$swaps = [];
    }

    public static function clearAllInstance(): void
    {
        self::$singleton = [];
    }

    public static function hasSwap(string $class_str): bool
    {
        return parent::hasSwap($class_str);
    }

    public static function hasBind(string $class_str, ?string $context): bool
    {
        return parent::hasBind($class_str, $context);
    }

    public static function hasInstance(string $class_str, ?string $context): bool
    {
        return isset(self::$singleton[self::hash($class_str) . self::hash($context)]);
    }
}
