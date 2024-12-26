<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Hash;
use Inilim\DI\ItemBind;
use Inilim\Singleton\SimpleSingleton;

/**
 * @psalm-type Concrete = class-string|object|\Closure(Bind,mixed[]):object
 */
final class Bind
{
    use SimpleSingleton;

    const
        KEY_CLASSIC       = 'c',
        KEY_CLASSIC_TAG   = 'ct',
        KEY_SINGLETON     = 's',
        KEY_SINGLETON_TAG = 'st',
        KEY_SWAP          = 'sw',
        KEY_SWAP_TAG      = 'swt';

    /** @var array<self::KEY_*,array<string,ItemBind>> */
    protected $map = [];

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return null|object
     */
    function get(string $abstract, $context = null, array $args = [])
    {
        $hash = Hash::getAbstract($abstract, $context);
        $item = $this->find(self::KEY_SWAP, $hash)
            ?? $this->find(self::KEY_CLASSIC, $hash)
            ?? $this->find(self::KEY_CLASSIC, $hash);
        /** @var ?ItemBind $item */

        return $item
            ? $item->resolveAndGetConcrete($args)
            : null;
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return null|object
     */
    function getByTag(string $tag, $context = null, array $args = [])
    {
        $hash = Hash::get($tag, $context);
        $item = $this->find(self::KEY_SWAP_TAG, $hash)
            ?? $this->find(self::KEY_CLASSIC_TAG, $hash)
            ?? $this->find(self::KEY_SINGLETON_TAG, $hash);
        /** @var ?ItemBind $item */

        return $item
            ? $item->resolveAndGetConcrete($args)
            : null;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function class(string $abstract, $concrete = null, $context = null)
    {
        $this->bind(self::KEY_CLASSIC, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classTag(string $tag, $concrete, $context = null)
    {
        $this->bind(self::KEY_CLASSIC_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function classIf(string $abstract, $concrete = null, $context = null)
    {
        $contextFiltered = [];
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            if (!isset($this->map[self::KEY_CLASSIC][Hash::getAbstract($abstract, $c)])) {
                $contextFiltered[] = $c;
            }
        }

        if ($contextFiltered) {
            $this->bind(self::KEY_CLASSIC, $abstract, $concrete, $contextFiltered);
        }

        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singleton(string $abstract, $concrete = null, $context = null)
    {
        $this->bind(self::KEY_SINGLETON, $abstract, $concrete, $context);
        return $this;
    }

    /**
     * @param non-empty-string $tag
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singletonTag(string $tag, $concrete = null, $context = null)
    {
        $this->bind(self::KEY_SINGLETON_TAG, $tag, $concrete, $context);
        return $this;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @return self
     */
    function singletonIf(string $abstract, $concrete = null, $context = null)
    {
        $contextFiltered = [];
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            if (!isset($this->map[self::KEY_SINGLETON][Hash::getAbstract($abstract, $c)])) {
                $contextFiltered[] = $c;
            }
        }

        if ($contextFiltered) {
            $this->bind(self::KEY_SINGLETON, $abstract, $concrete, $contextFiltered);
        }

        return $this;
    }

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param Concrete $swap
     * @return self
     */
    function swap(string $target, $swap)
    {
        $this->bind(self::KEY_SWAP, $target, $swap, null);
        return $this;
    }

    /**
     * @param non-empty-string $target
     * @param Concrete $swap
     * @return self
     */
    function swapTag(string $target, $swap)
    {
        $this->bind(self::KEY_SWAP_TAG, $target, $swap, null);
        return $this;
    }

    // ------------------------------------------------------------------
    // ___
    // ------------------------------------------------------------------

    /**
     * @param Bind::KEY_* $type
     * @param non-empty-string $hash
     */
    protected function find(string $type, string $hash): ?ItemBind
    {
        if (!isset($this->map[$type])) return null;
        return $this->map[$type][$hash] ?? null;
    }

    /**
     * @param Bind::KEY_* $type
     * @param class-string|non-empty-string $abstractOrTag
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     */
    protected function bind(
        string $type,
        string $abstractOrTag,
        $concrete = null,
        $context = null
    ): void {
        $this->map[$type] ??= [];

        $item  = new ItemBind($abstractOrTag, $type, $concrete);
        $isTag = $item->isTag();
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            $hash = $isTag
                ? Hash::get($abstractOrTag, $c)
                : Hash::getAbstract($abstractOrTag, $c);
            $this->map[$type][$hash] = $item;
        }
    }

    /**
     * @param Bind::KEY_* $type
     * @param class-string|non-empty-string $abstractOrTag
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     */
    protected function bindIf(
        string $type,
        string $abstractOrTag,
        $concrete = null,
        $context = null
    ): void {
        $isTag           = !\in_array($type, [BIND::KEY_CLASSIC, BIND::KEY_SINGLETON, BIND::KEY_SWAP], true);
        $contextFiltered = [];
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            $hash = $isTag
                ? Hash::get($abstractOrTag, $c)
                : Hash::getAbstract($abstractOrTag, $c);

            if (!isset($this->map[$type][$hash])) {
                $contextFiltered[] = $c;
            }
        }

        if ($contextFiltered) {
            $this->bind($type, $abstractOrTag, $concrete, $contextFiltered);
        }
    }
}
