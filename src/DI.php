<?php

namespace Inilim\DI;

use Inilim\DI\BindItem;

class DI
{
    /**
     * @var null|array<string,class-string|object>
     */
    protected static ?array $swaps = null;
    /**
     * @var array<string,object>
     */
    protected static array $singleton = [];
    /**
     * @var null|array<string,BindItem>
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
        $s = self::hasSwap($class_str);
        $b = self::hasBind($class_str, $context);
        if (!$s && !$b) {
            return self::make($class_str, $args);
        }

        if ($s) return self::getFromSwap($class_str, $args);
        return self::getFromBind($class_str, $context);
    }

    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param null|class-string $context controller
     * @return T
     */
    public static function getOrMakeInstance(string $class_str, ?string $context = null, ...$args)
    {
        return self::$singleton[self::hash($class_str) . self::hash($context)] ??= self::getOrMake($class_str, $context, ...$args);
    }

    /**
     * @param class-string $needs target
     * @param \Closure|class-string $give
     * @param null|class-string|class-string[] $when controller
     */
    public static function register(
        string $needs,
        \Closure|string $give,
        bool $is_singleton = false,
        null|string|array $when = null
    ): void {
        if (!\is_array($when)) $when = [$when];
        $b = new BindItem(
            give: $give,
            is_singleton: $is_singleton,
        );
        self::$binds ??= [];
        $n_hash = self::hash($needs);
        foreach ($when as $w) {
            self::$binds[$n_hash . self::hash($w)] = $b;
        }
        self::$binds[$n_hash] = $b;
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
    protected static function make(string $class_str, array $args = [])
    {
        if ($args) return new $class_str(...$args);
        return new $class_str;
    }

    protected static function getFromBind(string $class_str, ?string $context): object
    {
        $h_class = self::hash($class_str);
        $hash    = $h_class . self::hash($context);
        $b       = self::$binds[$hash] ?? self::$binds[$h_class];

        if ($b->is_singleton && (isset(self::$singleton[$hash]) || isset(self::$singleton[$h_class]))) {
            return self::$singleton[$hash] ?? self::$singleton[$h_class];
        }

        if (\is_string($b->give)) $obj = self::make($b->give);
        else $obj = ($b->give)();
        /** @var object $obj */

        if (!$b->is_singleton) return $obj;
        return self::$singleton[$hash] = self::$singleton[$h_class] = $obj;
    }

    /**
     * @param class-string $class_str
     * @param mixed[]|array{} $args
     */
    protected static function getFromSwap(string $class_str, array $args): object
    {
        $class_or_obj = self::swap($class_str);
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

    protected static function hasBind(string $class_str, ?string $context): bool
    {
        if (self::$binds === null) return false;
        $h_class = self::hash($class_str);
        return isset(self::$binds[$h_class . self::hash($context)])
            || isset(self::$binds[$h_class]);
    }

    /**
     * @param class-string $class_str
     * @return class-string|object
     */
    protected static function swap(string $class_str): string|object
    {
        return self::$swaps[self::hash($class_str)] ?? $class_str;
    }

    protected static function hash(?string $value): string
    {
        $c = \ltrim(($value ?? ''), '\\');
        if ($c === '') return '';
        return \md5($c);
    }
}
