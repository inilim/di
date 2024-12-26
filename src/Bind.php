<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Hash;
use Inilim\DI\ItemBind;
use Inilim\Singleton\SimpleSingleton;

/**
 * @api
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
    function getByAbstract(string $abstract, $context = null, array $args = [])
    {
        $item = $this->find([
            self::KEY_SWAP,
            self::KEY_CLASSIC,
            self::KEY_CLASSIC,
        ], Hash::get($abstract, $context));

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
        $item = $this->find([
            self::KEY_SWAP_TAG,
            self::KEY_CLASSIC_TAG,
            self::KEY_SINGLETON_TAG,
        ], Hash::get($tag, $context));

        return $item
            ? $item->resolveAndGetConcrete($args)
            : null;
    }

    // ---------------------------------------------
    // 
    // ---------------------------------------------

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
        $this->bindIf(self::KEY_CLASSIC, $abstract, $concrete, $context);
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
        $this->bindIf(self::KEY_SINGLETON, $abstract, $concrete, $context);
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
     * @param (self::KEY_*)|non-empty-list<(self::KEY_*)> $type
     * @param non-empty-string $hash
     */
    protected function find($type, string $hash): ?ItemBind
    {
        foreach ((array)$type as $t) {
            if (isset($this->map[$t][$hash])) {
                return $this->map[$t][$hash];
            }
        }
        return null;
    }

    /**
     * @param self::KEY_* $type
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
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            $this->map[$type][Hash::get($abstractOrTag, $c)] = $item;
        }
    }

    /**
     * @param self::KEY_* $type
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
        $contextFiltered = [];
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            if (!isset($this->map[$type][Hash::get($abstractOrTag, $c)])) {
                $contextFiltered[] = $c;
            }
        }

        if ($contextFiltered) {
            $this->bind($type, $abstractOrTag, $concrete, $contextFiltered);
        }
    }
}
