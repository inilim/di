<?php

declare(strict_types=1);

namespace Inilim\DI;

/**
 * @internal \Inilim\DI
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
        $t = \gettype($context);
        if ($t === 'object') {
            /** @var object $context */
            $context = \get_class($context);
        } elseif ($t === 'string') {
            /** @var string $context */
            $context = \ltrim($context, '\\');
        } else {
            $context = '';
        }

        return \md5($string . '|' . $context);
    }
}
