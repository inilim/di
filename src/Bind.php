<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\DI\Map;
use Inilim\DI\Swap;
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
     * @return self
     */
    function class(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classIf(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_CLASS, $abstract, $concrete, $context, true);
        return $this;
    }

    // ------------------------------------------------------------------
    // Class Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classTagIf(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_CLASS_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classTag(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_TAG, $tag, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // Singleton class
    // ------------------------------------------------------------------

    /**
     * @deprecated use Bind::classSingleton
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singleton(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classSingleton(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @deprecated use Bind::classSingletonList
     * @param class-string[] $abstract contract/interface OR realization/implementation
     * @return self
     */
    function singletonList(array $abstract)
    {
        $map = $this->mapInstance;
        foreach ($abstract as $item) {
            $map->bindOverwrite(Map::T_CLASS_SINGLE, $item, null, null);
        }
        return $this;
    }

    /**
     * @param class-string[] $abstract contract/interface OR realization/implementation
     * @return self
     */
    function classSingletonList(array $abstract)
    {
        $map = $this->mapInstance;
        foreach ($abstract as $item) {
            $map->bindOverwrite(Map::T_CLASS_SINGLE, $item, null, null);
        }
        return $this;
    }

    /**
     * @deprecated use Bind::classSingletonIf
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singletonIf(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classSingletonIf(string $abstract, $concrete = null, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // Singleton Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singletonTag(string $tag, $concrete = null, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singletonTagIf(string $tag, $concrete = null, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_CLASS_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // value
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param mixed|\Closure(DI $di, mixed[] $args):mixed $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function value(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_VALUE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param mixed|\Closure(DI $di, mixed[] $args):mixed $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function valueIf(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_VALUE_TAG, $tag, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // single value
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param mixed|\Closure(DI $di, mixed[] $args):mixed $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function valueSingle(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindOverwrite(Map::T_VALUE_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param mixed|\Closure(DI $di, mixed[] $args):mixed $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function valueSingleIf(string $tag, $concrete, $context = null)
    {
        $this->mapInstance->bindIf(Map::T_VALUE_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // Swap
    // ------------------------------------------------------------------

    /**
     * @deprecated use Swap::class
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function swap(string $target, $swap, $context = null)
    {
        Swap::self()->class($target, $swap, $context);
        return $this;
    }

    /**
     * @deprecated use Swap::classTag
     * @param non-empty-string $target tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function swapTag(string $target, $swap, $context = null)
    {
        Swap::self()->classTag($target, $swap, $context);
        return $this;
    }
}
