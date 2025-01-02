<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\DI\Hash;
use Inilim\DI\ItemBind;
use Inilim\Singleton\SimpleSingleton;

/**
 * @api
 * @psalm-type ClosureConcrete = \Closure(DI,mixed[]):object
 * @psalm-type ConcreteAll     = class-string|object|ClosureConcrete
 * @psalm-type Concrete        = class-string|ClosureConcrete
 */
final class Bind
{
    use SimpleSingleton;

    const
        KEY_CLASS         = 'c',
        KEY_CLASS_TAG     = 'ct',
        KEY_SINGLETON     = 's',
        KEY_SINGLETON_TAG = 'st',
        KEY_SWAP          = 'sw',
        KEY_SWAP_TAG      = 'swt';

    /** @var array<(self::KEY_*),array<string,ItemBind>> */
    protected $map = [];

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
        return $this->bindOrThrow(self::KEY_CLASS, $abstract, $concrete, $context, false, false);
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function classIf(string $abstract, $concrete = null, $context = null)
    {
        return $this->bindOrThrow(self::KEY_CLASS, $abstract, $concrete, $context, true, false);
    }

    // ------------------------------------------------------------------
    // Class Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function classTagIf(string $tag, $concrete, $context = null)
    {
        return $this->bindOrThrow(self::KEY_CLASS_TAG, $tag, $concrete, $context, true, false);
    }

    /**
     * @param non-empty-string $tag
     * @param Concrete $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function classTag(string $tag, $concrete, $context = null)
    {
        return $this->bindOrThrow(self::KEY_CLASS_TAG, $tag, $concrete, $context, false, false);
    }

    // ------------------------------------------------------------------
    // Singleton
    // ------------------------------------------------------------------

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singleton(string $abstract, $concrete = null, $context = null)
    {
        return $this->bindOrThrow(self::KEY_SINGLETON, $abstract, $concrete, $context);
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonIf(string $abstract, $concrete = null, $context = null)
    {
        return $this->bindOrThrow(self::KEY_SINGLETON, $abstract, $concrete, $context, true);
    }

    // ------------------------------------------------------------------
    // Singleton Tag
    // ------------------------------------------------------------------

    /**
     * @param non-empty-string $tag
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonTag(string $tag, $concrete = null, $context = null)
    {
        return $this->bindOrThrow(self::KEY_SINGLETON_TAG, $tag, $concrete, $context);
    }

    /**
     * @param non-empty-string $tag
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function singletonTagIf(string $tag, $concrete = null, $context = null)
    {
        return $this->bindOrThrow(self::KEY_SINGLETON_TAG, $tag, $concrete, $context, true);
    }

    // ------------------------------------------------------------------
    // Swap
    // ------------------------------------------------------------------

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param ConcreteAll $swap
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function swap(string $target, $swap, $context = null)
    {
        return $this->bindOrThrow(self::KEY_SWAP, $target, $swap, $context);
    }

    /**
     * @param non-empty-string $target
     * @param ConcreteAll $swap
     * @param null|class-string|class-string[] $context
     * @throws \InvalidArgumentException
     * @return self
     */
    function swapTag(string $target, $swap, $context = null)
    {
        return $this->bindOrThrow(self::KEY_SWAP_TAG, $target, $swap, $context);
    }

    // ------------------------------------------------------------------
    // 
    // ------------------------------------------------------------------

    /**
     * @param self::KEY_* $type
     * @param class-string|non-empty-string $target
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @param bool $isIf
     * @param bool $allowConcreteAnyObject
     * @return self
     */
    protected function bindOrThrow(
        $type,
        $target,
        $concrete,
        $context = null,
        $isIf = false,
        $allowConcreteAnyObject = true
    ) {
        if (($allowConcreteAnyObject && \is_object($concrete)) || $this->checkType($concrete)) {
            $isIf
                ? $this->bindIf($type, $target, $concrete, $context)
                : $this->bind($type, $target, $concrete, $context);

            return $this;
        }

        throw new \InvalidArgumentException();
    }

    /**
     * @param (self::KEY_*)|non-empty-list<(self::KEY_*)> $type
     * @param non-empty-string $hash
     * @return null|ItemBind
     */
    protected function find($type, $hash)
    {
        foreach ((array)$type as $t) {
            $r = $this->map[$t][$hash] ?? null;
            if ($r !== null) {
                return $r;
            }
        }
        return null;
    }

    /**
     * @param self::KEY_* $type
     * @param class-string|non-empty-string $abstractOrTag
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @return void
     */
    protected function bind(
        $type,
        $abstractOrTag,
        $concrete = null,
        $context = null
    ) {
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
     * @param null|ConcreteAll $concrete
     * @param null|class-string|class-string[] $context
     * @return void
     */
    protected function bindIf(
        $type,
        $abstractOrTag,
        $concrete = null,
        $context = null
    ) {
        $contextFiltered = [];
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            if (!isset($this->map[$type][Hash::get($abstractOrTag, $c)])) {
                $contextFiltered[] = $c;
            }
        }

        if ($contextFiltered) {
            // @phpstan-ignore-next-line
            $this->bind($type, $abstractOrTag, $concrete, $contextFiltered);
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function checkType($value)
    {
        return $value === null || \is_string($value) || $value instanceof \Closure;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return null|object
     */
    protected function getByAbstract(string $abstract, $context = null, array $args = [])
    {
        $item = $this->find([
            self::KEY_SWAP,
            self::KEY_CLASS,
            self::KEY_SINGLETON,
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
    protected function getByTag(string $tag, $context = null, array $args = [])
    {
        $item = $this->find([
            self::KEY_SWAP_TAG,
            self::KEY_CLASS_TAG,
            self::KEY_SINGLETON_TAG,
        ], Hash::get($tag, $context));

        return $item
            ? $item->resolveAndGetConcrete($args)
            : null;
    }
}
