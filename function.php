<?php

if (!\function_exists('DI')) {
    /**
     * @template T of object
     * @param class-string $dep
     * @param null|class-string|object|mixed[] $args_or_context array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    function DI(string $dep, $args_or_context = null, $context = null)
    {
        // 
    }
}
