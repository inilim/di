<?php

use Inilim\DI\DI;

if (!\function_exists('DI')) {
    /**
     * @template T of object
     * @param class-string $dep
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    function DI(string $dep, $argsOrContext = null, $context = null)
    {
        static $o = null;
        $o ??= DI::self();

        $args = [];
        if (\is_array($argsOrContext)) {
            $args = $argsOrContext;
        } else {
            $context = $argsOrContext;
        }

        return $o->getByAbstract($dep, $context, $args);
    }
}

if (!\function_exists('DITag')) {
    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return ?object
     */
    function DITag(string $tag, $argsOrContext = null, $context = null)
    {
        static $o = null;
        $o ??= DI::self();

        $args = [];
        if (\is_array($argsOrContext)) {
            $args = $argsOrContext;
        } else {
            $context = $argsOrContext;
        }

        return $o->getByTag($tag, $context, $args);
    }
}
