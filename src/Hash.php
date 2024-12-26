<?php

declare(strict_types=1);

namespace Inilim\DI;

/**
 * @internal \Inilim\DI
 * @psalm-internal \Inilim\DI
 * @psalm-type HashStr = non-empty-string
 */
final class Hash
{
    /**
     * @param non-empty-string $str
     * @param null|class-string|object $context
     * @return HashStr
     */
    static function get(string $str, $context): string
    {
        if (\is_object($context)) {
            $context = \get_class($context);
        } elseif (\is_string($context)) {
            $context = \ltrim($context, '\\');
        } else {
            $context = '';
        }

        return \md5($str . '|' . $context);
    }

    /**
     * @param class-string $abstract
     * @param null|class-string|object $context
     * @return HashStr
     */
    static function getAbstract(string $abstract, $context): string
    {
        return self::get(\ltrim($abstract, '\\'), $context);
    }
}
