<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\Singleton\SimpleSingleton;

/**
 * @api
 * 
 * @phpstan-type TypeConcreteAll class-string|object|\Closure(DI $di, mixed[] $args): object
 * @phpstan-type TypeConcrete class-string|\Closure(DI $di, mixed[] $args): object
 */
final class Bind
{
    use SimpleSingleton;

    protected Map $mapInstance;

    private function __construct()
    {
        $this->mapInstance = Map::self();
    }

    function clear(): self
    {
        $this->mapInstance->map = [];
        return $this;
    }

    // ---------------------------------------------
    // Class
    // ---------------------------------------------

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure(DI,mixed[]):object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function class(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_CLASS, $abstract, $concrete, $context, false, false);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function classIf(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_CLASS, $abstract, $concrete, $context, true, false);
        return $this;
    }

    // ------------------------------------------------------------------
    // Class Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function classTagIf(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_CLASS_TAG, $tag, $concrete, $context, true, false);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function classTag(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_CLASS_TAG, $tag, $concrete, $context, false, false);
        return $this;
    }

    // ------------------------------------------------------------------
    // Singleton
    // ------------------------------------------------------------------

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singleton(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_SINGLETON, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string[] $abstract contract/interface OR realization/implementation
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonList(array $abstract)
    {
        $map = $this->mapInstance;
        foreach ($abstract as $item) {
            $map->bindOrThrow(Map::KEY_SINGLETON, $item, null, null, false, true);
        }
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonIf(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_SINGLETON, $abstract, $concrete, $context, true);
        return $this;
    }

    // ------------------------------------------------------------------
    // Singleton Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonTag(string $tag, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_SINGLETON_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonTagIf(string $tag, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_SINGLETON_TAG, $tag, $concrete, $context, true);
        return $this;
    }

    // ------------------------------------------------------------------
    // Swap
    // ------------------------------------------------------------------

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function swap(string $target, $swap, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_SWAP_CLASS, $target, $swap, $context);
        return $this;
    }

    /**
     * @param non-empty-string $target
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function swapTag(string $target, $swap, $context = null)
    {
        $this->mapInstance->bindOrThrow(Map::KEY_SWAP_TAG, $target, $swap, $context);
        return $this;
    }
}
