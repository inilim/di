<?php

declare(strict_types=1);

namespace Inilim\DI\Swap;

use Inilim\DI\Hash;

final class Bind
{
    /**
     * @var array<string,class-string|object|\Closure>
     */
    protected ?array $map = null;

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure $swap
     * @param null|class-string|class-string[] $context
     */
    function bind(string $target, $swap, $context = null): void
    {
        $_target = \ltrim($target, '\\');
        $_context  = \is_array($context) ? $context : [$context];

        $this->map ??= [];

        foreach ($_context as $c) {
            $this->map[Hash::getWithContext($_target, $c)] = $swap;
        }
    }

    /**
     * @return class-string|object|\Closure|null
     */
    function get(string $hash)
    {
        $this->map ??= [];
        return $this->map[$hash] ?? null;
    }

    function exists(): bool
    {
        return $this->map !== null;
    }
}
