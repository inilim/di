<?php

use Inilim\DI\DI;

if (!function_exists('_DI')) {
    /**
     * получить зависимость, контекст будет определен автоматически
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    function _DI(string $class_str, ...$args)
    {
        $context = \array_column(
            \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2),
            'class'
        )[0] ?? null;

        return DI::getOrMake($class_str, $context, ...$args);
    }
}

if (!function_exists('_DIValue')) {
    /**
     * @return mixed
     */
    function _DIValue(string $key, ?string $context = null)
    {
        $context ??= \array_column(
            \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2),
            'class'
        )[0] ?? null;

        return DI::getPrimitive($key, $context);
    }
}


if (!function_exists('_DIWithoutContext')) {
    /**
     * Получить зависимость без указания контекста
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    function _DIWithoutContext(string $class_str, ...$args)
    {
        return DI::getOrMake($class_str, null, ...$args);
    }
}

if (!function_exists('_DIContext')) {
    /**
     * получить зависимость с возможностью указать контекст
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param null|class-string $context
     * @return T
     */
    function _DIContext(string $class_str, ?string $context = null, ...$args)
    {
        return DI::getOrMake($class_str, $context, ...$args);
    }
}

if (!function_exists('_DISingleton')) {
    /**
     * в случаи если нету прослойки для регистрации
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @return T
     */
    function _DISingleton(string $class_str, ...$args)
    {
        return DI::getOrBindSingleton($class_str, ...$args);
    }
}
