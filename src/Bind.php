<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Hash;
use Inilim\Singleton\SimpleSingleton;

final class Bind
{
    use SimpleSingleton;

    /** @var ?array<string,class-string|object|\Closure(Bind, mixed[]):object> */
    protected $mapClass = null;
    /** @var ?array<string,class-string|object|\Closure(Bind, mixed[]):object> */
    protected $mapSingleton = null;
    /** @var ?array<string,class-string|object|\Closure(Bind, mixed[]):object> */
    protected $mapSwap = null;

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return null|object
     */
    function resolveAndGet(string $abstract, $context = null, array $args = [])
    {
        $h = Hash::getAbstract($abstract, $context);

        if ($this->mapSwap !== null && isset($this->mapSwap[$h])) {
            return $this->resolve($this->mapSwap[$h], $args);
        } elseif ($this->mapClass !== null && isset($this->mapClass[$h])) {
            return $this->resolve($this->mapClass[$h], $args);
        } elseif ($this->mapSingleton !== null && isset($this->mapSingleton[$h])) {
            return $this->mapSingleton[$h] = $this->resolve($this->mapSingleton[$h], $args);
        }
        return null;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @return null|class-string|object|\Closure(Bind, mixed[]):object
     */
    // function get(string $abstract, $context = null)
    // {
    //     $hash = Hash::getAbstract($abstract, $context);

    //     if ($this->mapSwap !== null && isset($this->mapSwap[$hash])) {
    //         return $this->mapSwap[$hash];
    //     } elseif ($this->mapClass !== null && isset($this->mapClass[$hash])) {
    //         return $this->mapClass[$hash];
    //     } elseif ($this->mapSingleton !== null && isset($this->mapSingleton[$hash])) {
    //         return $this->mapSingleton[$hash];
    //     }
    //     return null;
    // }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(Bind, mixed[]):object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function class(string $abstract, $concrete = null, $context = null)
    {
        $abstract = \ltrim($abstract, '\\');
        $_context = \is_array($context) ? $context : [$context];

        $this->mapClass ??= [];

        foreach ($_context as $c) {
            $this->mapClass[Hash::getAbstract($abstract, $c)] = $concrete ?? $abstract;
        }

        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(Bind, mixed[]):object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classIf(string $abstract, $concrete = null, $context = null)
    {
        if ($this->mapClass === null || !isset($this->mapClass[Hash::getAbstract($abstract, $context)])) {
            return $this->class($abstract, $concrete, $context);
        }

        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(Bind, mixed[]):object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singleton(string $abstract, $concrete = null, $context = null)
    {
        $abstract = \ltrim($abstract, '\\');
        $_context = \is_array($context) ? $context : [$context];

        $this->mapSingleton ??= [];

        foreach ($_context as $c) {
            $this->mapSingleton[Hash::getAbstract($abstract, $c)] = $concrete ?? $abstract;
        }

        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(Bind, mixed[]):object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singletonIf(string $abstract, $concrete = null, $context = null)
    {
        if ($this->mapSingleton === null || !isset($this->mapSingleton[Hash::getAbstract($abstract, $context)])) {
            return $this->singleton($abstract, $concrete, $context);
        }

        return $this;
    }

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure(Bind, mixed[]):object $swap
     * @return self
     */
    function swap(string $target, $swap)
    {
        $target = \ltrim($target, '\\');

        $this->mapSwap ??= [];

        $this->mapSwap[Hash::getAbstract($target, null)] = $swap;

        return $this;
    }

    // ------------------------------------------------------------------
    // ___
    // ------------------------------------------------------------------

    /**
     * @param class-string|object|\Closure(Bind, mixed[]):object $concrete
     * @param mixed[] $args
     * @return object
     */
    protected function resolve($concrete, array $args)
    {
        if (\is_string($concrete)) {
            return new $concrete(...$args);
        }

        if ($concrete instanceof \Closure) {
            return $concrete->__invoke($this, $args);
        }

        return $concrete;
    }
}
