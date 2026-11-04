<?php

use Inilim\DI\DI;

if (!\function_exists('DI')) {
    /**
     * @deprecated use DIClass
     * 
     * @template T of object
     * @param class-string<T> $dependence
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    function DI(string $dependence, $argsOrContext = null, $context = null): object
    {
        static $o = null;
        if (null === $o) {
            $o = DI::self();
        }
        return $o->class($dependence, $argsOrContext, $context);
    }
}

if (!\function_exists('DIClass')) {
    /**
     * @template T of object
     * @param class-string<T> $dependence
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    function DIClass(string $dependence, $argsOrContext = null, $context = null): object
    {
        static $o = null;
        if (null === $o) {
            $o = DI::self();
        }
        return $o->class($dependence, $argsOrContext, $context);
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
        if (null === $o) {
            $o = DI::self();
        }
        return $o->tag($tag, $argsOrContext, $context);
    }
}

if (!\function_exists('DIVal')) {
    /**
     * primitive
     * @param non-empty-string $tag
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return mixed
     */
    function DIVal(string $tag, $argsOrContext = null, $context = null)
    {
        static $o = null;
        if (null === $o) {
            $o = DI::self();
        }
        return $o->value($tag, $argsOrContext, $context);
    }
}
