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
        return DI::getOrMake(
            $class_str,
            _DIDefineContext(2)[1] ?? null,
            ...$args
        );
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

if (!function_exists('_DIExplicitContext')) {
    /**
     * получить зависимость с явным указанием контекста
     * 
     * @template T of object
     * @param class-string<T> $class_str
     * @param mixed[]|array{} $args
     * @param class-string $context
     * @return T
     */
    function _DIExplicitContext(string $class_str, string $context, ...$args)
    {
        return DI::getOrMake($class_str, $context, ...$args);
    }
}

if (!function_exists('_DISingleton')) {
    /**
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

if (!function_exists('_DIDefineContext')) {
    /**
     * @return class-string[]|array{}
     */
    function _DIDefineContext(int $size = 5): array
    {
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, ($size + 1));
        \array_shift($trace);
        return \array_column($trace, 'class');
    }
}
