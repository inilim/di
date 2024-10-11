<?php

declare(strict_types=1);

namespace Inilim\DI\Swap;

use Inilim\DI\Hash;
use Inilim\DI\Swap\Bind;

final class DISwap
{
    protected Bind $bind;

    function __construct(Bind $bind)
    {
        $this->bind = $bind;
    }

    function hasBindClass(): bool
    {
        return $this->bind->hasBindClass();
    }

    function hasBindPrimitive(): bool
    {
        return $this->bind->hasBindPrimitive();
    }

    /**
     * @param null|class-string|class-string[] $context
     * @return mixed
     */
    function getPrimitive(string $key, $context = null)
    {
        return $this->bind->getPrimitive(Hash::getWithContext($key, $context));
    }

    function getClass(string $target, $context = null, ...$args): ?object
    {
        $_target = \ltrim($target, '\\');

        $hash = Hash::getWithContext($_target, $context);
        $swap = $this->bind->getClass($hash);

        if ($swap === null) {
            return null;
        }

        if (\is_object($swap)) {
            if ($swap instanceof \Closure) {
                return $swap->__invoke(...$args);
            }
            return $swap;
        }

        if (!\class_exists($swap, true)) {
            throw new \Exception(\sprintf(
                'class "%s" not found',
                $swap
            ));
        }

        return new $swap(...$args);
    }
}
