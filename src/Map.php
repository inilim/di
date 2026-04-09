<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\DI\Hash;
use Inilim\DI\ItemBind;
use Inilim\Singleton\SimpleSingleton;

/**
 * @internal \Inilim\DI
 */
final class Map
{
    use SimpleSingleton;

    const
        T_CLASS            = 1 << 0,  // 1
        T_CLASS_TAG        = 1 << 1,  // 2
        T_CLASS_SINGLE     = 1 << 2,  // 4
        T_CLASS_SINGLE_TAG = 1 << 3,  // 8
        T_VALUE_TAG        = 1 << 4,  // 16
        T_VALUE_SINGLE_TAG = 1 << 5,  // 32
        T_VALUE_TAG_SWAP   = 1 << 6,  // 64
        T_CLASS_SWAP       = 1 << 7,  // 128
        T_CLASS_TAG_SWAP   = 1 << 8,  // 256
        // statuses
        IS_TAG = self::T_VALUE_TAG | self::T_CLASS_TAG | self::T_CLASS_SINGLE_TAG | self::T_VALUE_SINGLE_TAG | self::T_VALUE_TAG_SWAP | self::T_CLASS_TAG_SWAP,
        IS_SINGLE = self::T_CLASS_SINGLE | self::T_CLASS_SINGLE_TAG | self::T_VALUE_SINGLE_TAG,
        IS_SWAP = self::T_CLASS_SWAP | self::T_CLASS_TAG_SWAP | self::T_VALUE_TAG_SWAP,
        IS_CLASS = self::T_CLASS | self::T_CLASS_TAG | self::T_CLASS_SINGLE | self::T_CLASS_SINGLE_TAG | self::T_CLASS_SWAP | self::T_CLASS_TAG_SWAP,
        IS_VALUE = self::T_VALUE_TAG | self::T_VALUE_SINGLE_TAG | self::T_VALUE_TAG_SWAP
        // 
    ;

    /** @var array<(self::T_*),array<string,ItemBind>> */
    public array $map = [];

    /**
     * @param non-empty-list<(self::T_*)> $type
     * @param non-empty-string $hash
     */
    public function find(array $type, string $hash): ?ItemBind
    {
        foreach ($type as $t) {
            $r = $this->map[$t][$hash] ?? null;
            if ($r !== null) {
                return $r;
            }
        }
        return null;
    }

    /**
     * @param self::T_* $type
     * @param class-string|non-empty-string $abstractOrTag
     * @param mixed $concrete
     * @param null|class-string|class-string[] $context
     */
    public function bindOverwrite(
        int $type,
        string $abstractOrTag,
        $concrete,
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
     * @param self::T_* $type
     * @param class-string|non-empty-string $abstractOrTag
     * @param mixed $concrete
     * @param null|class-string|class-string[] $context
     */
    public function bindIf(
        int $type,
        string $abstractOrTag,
        $concrete,
        $context = null
    ): void {
        $contextFiltered = [];
        $hasNull = false;
        foreach (
            (\is_array($context) ? $context : [$context]) as $c
        ) {
            if (!isset($this->map[$type][Hash::get($abstractOrTag, $c)])) {
                if ($c === null) {
                    $hasNull = true;
                } else {
                    $contextFiltered[] = $c;
                }
            }
        }

        if ($contextFiltered) {
            $this->bindOverwrite($type, $abstractOrTag, $concrete, $contextFiltered);
        }

        if ($hasNull) {
            $this->bindOverwrite($type, $abstractOrTag, $concrete, null);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return ?T
     */
    public function getClassByAbstract(string $abstract, $context = null, array $args = []): ?object
    {
        $item = $this->find([
            self::T_CLASS_SWAP,
            self::T_CLASS,
            self::T_CLASS_SINGLE,
        ], Hash::get($abstract, $context));

        return $item
            ? $item->resolveAndGet($args)
            : null;
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object $context
     * @param mixed[] $args
     */
    public function getClassByTag(string $tag, $context = null, array $args = []): ?object
    {
        $item = $this->find([
            self::T_CLASS_TAG_SWAP,
            self::T_CLASS_TAG,
            self::T_CLASS_SINGLE_TAG,
        ], Hash::get($tag, $context));

        return $item
            ? $item->resolveAndGet($args)
            : null;
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return mixed
     */
    public function getValueByTag(string $tag, $context = null, array $args = [])
    {
        $item = $this->find([
            self::T_VALUE_TAG_SWAP,
            self::T_VALUE_TAG,
            self::T_VALUE_SINGLE_TAG,
        ], Hash::get($tag, $context));

        return $item
            ? $item->resolveAndGet($args)
            : null;
    }
}
