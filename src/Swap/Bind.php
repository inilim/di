<?php

declare(strict_types=1);

namespace Inilim\DI\Swap;

use Inilim\DI\Hash;

final class Bind
{
    /**
     * @var null|array<string,class-string|object|\Closure>
     */
    protected ?array $mapClass     = null;
    /**
     * @var null|array<string,mixed>
     */
    protected ?array $mapPrimitive = null;

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure $swap
     * @param null|class-string|class-string[] $context
     */
    function bindClass(string $target, $swap, $context = null): void
    {
        $_target = \ltrim($target, '\\');
        $_context  = \is_array($context) ? $context : [$context];

        $this->mapClass ??= [];

        foreach ($_context as $c) {
            $this->mapClass[Hash::getWithContext($_target, $c)] = $swap;
        }
    }

    /**
     * @param non-empty-string $key
     * @param mixed $swap return value
     * @param null|class-string|class-string[] $context
     */
    function bind(string $key, $swap, $context = null): void
    {
        $this->mapPrimitive ??= [];

        $_context  = \is_array($context) ? $context : [$context];

        foreach ($_context as $c) {
            $this->mapPrimitive[Hash::getWithContext($key, $c)] = $swap;
        }
    }

    /**
     * @return class-string|object|\Closure|null
     */
    function getClass(string $hash)
    {
        $this->mapClass ??= [];
        return $this->mapClass[$hash] ?? null;
    }

    function hasBindClass(): bool
    {
        return $this->mapClass !== null;
    }

    /**
     * @return mixed
     */
    function getPrimitive(string $hash)
    {
        $this->mapPrimitive ??= [];
        return $this->mapPrimitive[$hash] ?? null;
    }

    function hasBindPrimitive(): bool
    {
        return $this->mapPrimitive !== null;
    }
}
