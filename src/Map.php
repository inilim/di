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
        KEY_CLASS         = 'c',
        KEY_CLASS_TAG     = 'ct',
        KEY_SINGLETON     = 's',
        KEY_SINGLETON_TAG = 'st',
        // KEY_VALUE         = 'v',
        // KEY_SINGLE_VALUE  = 'sv',
        // KEY_SWAP_VALUE    = 'swv',
        KEY_SWAP_CLASS    = 'swc',
        KEY_SWAP_TAG      = 'swt'
        // 
    ;

    /** @var array<(self::KEY_*),array<string,ItemBind>> */
    public array $map = [];

    /**
     * @param self::KEY_* $type
     * @param class-string|non-empty-string $target
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     * @param bool $isIf
     * @param bool $allowConcreteAnyObject
     */
    public function bindOrThrow(
        string $type,
        string $target,
        $concrete,
        $context                     = null,
        bool $isIf                   = false,
        bool $allowConcreteAnyObject = true
    ): void {
        if (
            ($allowConcreteAnyObject && \is_object($concrete))
            ||
            $this->checkTypeConcrete($concrete)
        ) {
            $isIf
                ? $this->bindIf($type, $target, $concrete, $context)
                : $this->bind($type, $target, $concrete, $context);

            return;
        }

        throw new \InvalidArgumentException;
    }

    /**
     * @param (self::KEY_*)|non-empty-list<(self::KEY_*)> $type
     * @param non-empty-string $hash
     */
    public function find($type, string $hash): ?ItemBind
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
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     */
    public function bind(
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
     * @param null|class-string|object|\Closure(DI $di, mixed[] $args): object $concrete
     * @param null|class-string|class-string[] $context
     */
    public function bindIf(
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
            // @phpstan-ignore-next-line
            $this->bind($type, $abstractOrTag, $concrete, $contextFiltered);
        }
    }

    /**
     * @param mixed $value
     * @phpstan-assert-if-true null|string|\Closure $value
     */
    public function checkTypeConcrete($value): bool
    {
        return $value === null || \is_string($value) || $value instanceof \Closure;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @param mixed[] $args
     */
    public function getByAbstract(string $abstract, $context = null, array $args = []): ?object
    {
        $item = $this->find([
            self::KEY_SWAP_CLASS,
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
     */
    public function getByTag(string $tag, $context = null, array $args = []): ?object
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

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return mixed
     */
    // public function getValueByTag(string $tag, $context = null, array $args = [])
    // {
    // $item = $this->find([
    // self::KEY_SWAP_VALUE,
    // self::KEY_VALUE,
    // self::KEY_SINGLE_VALUE,
    // ], Hash::get($tag, $context));

    // return $item
    // ? $item->resolveAndGetConcrete($args)
    // : null;
    // }
}
