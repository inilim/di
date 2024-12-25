<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Hash;

final class Bind
{
    /** @var ?array<string,class-string|object|\Closure():object> */
    protected $mapClass = null;
    /** @var ?array<string,class-string|object|\Closure():object> */
    protected $mapSingleton = null;
    /** @var ?array<string,class-string|object|\Closure():object> */
    protected $mapSwap = null;

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure():object $concrete
     * @param null|class-string|class-string[] $context
     */
    function class(string $abstract, $concrete = null, $context = null): void
    {
        $abstract = \ltrim($abstract, '\\');
        $_context = \is_array($context) ? $context : [$context];

        $this->mapClass ??= [];

        foreach ($_context as $c) {
            $this->mapClass[Hash::getAbstract($abstract, $c)] = $concrete ?? $abstract;
        }
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure():object $concrete
     * @param null|class-string|class-string[] $context
     */
    function singleton(string $abstract, $concrete = null, $context = null): void
    {
        $abstract = \ltrim($abstract, '\\');
        $_context = \is_array($context) ? $context : [$context];

        $this->mapSingleton ??= [];

        foreach ($_context as $c) {
            $this->mapSingleton[Hash::getAbstract($abstract, $c)] = $concrete ?? $abstract;
        }
    }

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure():object $swap
     */
    function swap(string $target, $swap): void
    {
        $target = \ltrim($target, '\\');

        $this->mapSwap ??= [];

        $this->mapSwap[Hash::getAbstract($target, null)] = $swap;
    }
}
