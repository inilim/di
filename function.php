<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;

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
        static $o = null;
        $o ??= new DI(Bind::self());

        $args = [];
        if (\is_array($args_or_context)) {
            $args = $args_or_context;
        } else {
            $context = $args_or_context;
        }

        return $o->get($dep, $context, $args);
    }
}
