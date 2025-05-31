<?php

use Inilim\DI\DI;

if (!\function_exists('DI')) {
    /**
     * @template T of object
     * @param class-string<T> $dependence
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    function DI(string $dependence, $argsOrContext = null, $context = null): object
    {
        static $o = null;
        if ($o === null) {
            $o = DI::self();
        }
        return $o->DI($dependence, $argsOrContext, $context);
    }
}

if (!\function_exists('DITag')) {
    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     */
    function DITag(string $tag, $argsOrContext = null, $context = null): ?object
    {
        static $o = null;
        if ($o === null) {
            $o = DI::self();
        }
        return $o->DITag($tag, $argsOrContext, $context);
    }
}
