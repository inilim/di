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
 * @phpstan-type T_Bind_Context null|class-string|object|(class-string|object)[]
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
     * @param T_Bind_Context $context
     */
    function class(string $abstract, $concrete = null, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure(DI $di, mixed[] $args): object $concrete
     * @param T_Bind_Context $context
     */
    function classIf(string $abstract, $concrete = null, $context = null): self
    {
        $this->mapInstance->bindIf(Map::T_CLASS, $abstract, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // Class Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param class-string|\Closure(DI $di, mixed[] $args):object $concrete
     * @param T_Bind_Context $context
     */
    function classTagIf(string $tag, $concrete, $context = null): self
    {
        $this->mapInstance->bindIf(Map::T_CLASS_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param class-string|\Closure(DI $di, mixed[] $args):object $concrete
     * @param T_Bind_Context $context
     */
    function classTag(string $tag, $concrete, $context = null): self
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
     * @param T_Bind_Context $context
     */
    function singleton(string $abstract, $concrete = null, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param T_Bind_Context $context
     */
    function classSingleton(string $abstract, $concrete = null, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @deprecated use Bind::classSingletonList
     * @param iterable<class-string> $abstract contract/interface OR realization/implementation
     */
    function singletonList(iterable $abstract): self
    {
        $map = $this->mapInstance;
        foreach ($abstract as $item) {
            $map->bindOverwrite(Map::T_CLASS_SINGLE, $item, null, null);
        }
        return $this;
    }

    /**
     * @param iterable<class-string> $abstract contract/interface OR realization/implementation
     */
    function classSingletonList(iterable $abstract): self
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
     * @param T_Bind_Context $context
     */
    function singletonIf(string $abstract, $concrete = null, $context = null): self
    {
        $this->mapInstance->bindIf(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param T_Bind_Context $context
     */
    function classSingletonIf(string $abstract, $concrete = null, $context = null): self
    {
        $this->mapInstance->bindIf(Map::T_CLASS_SINGLE, $abstract, $concrete, $context);
        return $this;
    }

    // ------------------------------------------------------------------
    // Singleton Tag
    // ------------------------------------------------------------------

    /**
     * @deprecated use Bind::classSingletonTag
     * @param non-empty-string $tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args):object $concrete
     * @param T_Bind_Context $context
     */
    function singletonTag(string $tag, $concrete, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args):object $concrete
     * @param T_Bind_Context $context
     */
    function classSingletonTag(string $tag, $concrete, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_CLASS_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @deprecated use Bind::classSingletonTagIf
     * @param non-empty-string $tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args):object $concrete
     * @param T_Bind_Context $context
     */
    function singletonTagIf(string $tag, $concrete, $context = null): self
    {
        $this->mapInstance->bindIf(Map::T_CLASS_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args):object $concrete
     * @param T_Bind_Context $context
     */
    function classSingletonTagIf(string $tag, $concrete, $context = null): self
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
     * @param T_Bind_Context $context
     */
    function value(string $tag, $concrete, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_VALUE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param mixed|\Closure(DI $di, mixed[] $args):mixed $concrete
     * @param T_Bind_Context $context
     */
    function valueIf(string $tag, $concrete, $context = null): self
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
     * @param T_Bind_Context $context
     */
    function valueSingle(string $tag, $concrete, $context = null): self
    {
        $this->mapInstance->bindOverwrite(Map::T_VALUE_SINGLE_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param mixed|\Closure(DI $di, mixed[] $args):mixed $concrete
     * @param T_Bind_Context $context
     */
    function valueSingleIf(string $tag, $concrete, $context = null): self
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
     * @param T_Bind_Context $context
     */
    function swap(string $target, $swap, $context = null): self
    {
        Swap::self()->class($target, $swap, $context);
        return $this;
    }

    /**
     * @deprecated use Swap::classTag
     * @param non-empty-string $target tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param T_Bind_Context $context
     */
    function swapTag(string $target, $swap, $context = null): self
    {
        Swap::self()->classTag($target, $swap, $context);
        return $this;
    }
}
