<?php

namespace Inilim\DI;

final class Hash
{
    /**
     * @param non-empty-string $key
     * @param null|class-string|object $context
     */
    static function getWithContext(string $str, $context): string
    {
        if (\is_object($context)) {
            $_context = \get_class($context);
        } elseif (\is_string($context)) {
            $_context = \ltrim($context, '\\');
        } else {
            $_context = '';
        }

        return \md5($str . '|' . $_context);
    }
}
