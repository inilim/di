<?php

use Inilim\DI\DI;

if (!function_exists('_DI')) {
    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param null|class-string $context controller
     * @return T
     */
    function _DI(string $class_str, ?string $context = null, ...$args)
    {
        return DI::getOrMake($class_str, $context, ...$args);
    }
}

if (!function_exists('_DISingleton')) {
    /**
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param null|class-string $context controller
     * @return T
     */
    function _DISingleton(string $class_str, ?string $context = null, ...$args)
    {
        return DI::getOrMakeInstance($class_str, $context, ...$args);
    }
}
