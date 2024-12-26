<?php

declare(strict_types=1);

namespace Inilim\DI;

/**
 * @internal \Inilim\DI
 * @psalm-internal \Inilim\DI
 */
final class Hash
{
    /**
     * @param non-empty-string $string
     * @param null|class-string|object $context
     * @return non-empty-string
     */
    static function get(string $string, $context): string
    {
        if (\is_object($context)) {
            $context = \get_class($context);
        } elseif (\is_string($context)) {
            $context = \ltrim($context, '\\');
        } else {
            $context = '';
        }

        return \md5($string . '|' . $context);
    }
}
