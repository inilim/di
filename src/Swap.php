<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\DI\Hash;
use Inilim\DI\Map;
use Inilim\Singleton\SimpleSingleton;

/**
 * @api
 * for tests
 * 
 * @phpstan-type T_Bind_Context null|class-string|object|(class-string|object)[]
 */
final class Swap
{
    use SimpleSingleton;

    protected Map $mapInstance;

    private function __construct()
    {
        $this->mapInstance = Map::self();
    }

    /**
     * @param class-string $target contract/interface OR realization/implementation
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param T_Bind_Context $context
     * @return self
     */
    function class(string $target, $swap, $context = null)
    {
        $item = $this->find([
            Map::T_CLASS_SWAP,
            Map::T_CLASS,
            Map::T_CLASS_SINGLE,
        ], $target, $context);

        $this->checkItem($item);

        $this->mapInstance->bindOverwrite(Map::T_CLASS_SWAP, $target, $swap, $context);
        return $this;
    }

    /**
     * @param non-empty-string $target tag
     * @param class-string|object|\Closure(DI $di, mixed[] $args): object $swap
     * @param T_Bind_Context $context
     * @return self
     */
    function classTag(string $target, $swap, $context = null)
    {
        $item = $this->find([
            Map::T_CLASS_TAG_SWAP,
            Map::T_CLASS_TAG,
            Map::T_CLASS_SINGLE_TAG,
        ], $target, $context);

        $this->checkItem($item);

        $this->mapInstance->bindOverwrite(Map::T_CLASS_TAG_SWAP, $target, $swap, $context);
        return $this;
    }

    /**
     * @param non-empty-string $target tag
     * @param mixed $swap
     * @param T_Bind_Context $context
     * @return self
     */
    function value(string $target, $swap, $context = null)
    {
        $item = $this->find([
            Map::T_VALUE_TAG_SWAP,
            Map::T_VALUE_TAG,
            Map::T_VALUE_SINGLE_TAG,
        ], $target, $context);

        $this->checkItem($item);

        $this->mapInstance->bindOverwrite(Map::T_VALUE_TAG_SWAP, $target, $swap, $context);
        return $this;
    }

    /**
     * @param non-empty-list<(Map::T_*)> $types
     * @param non-empty-string|class-string $target
     * @param T_Bind_Context $context
     */
    protected function find(array $types, string $target, $context = null): ?ItemBind
    {
        $map = $this->mapInstance;
        foreach (
            \is_array($context) ? $context : [$context] as $c
        ) {
            $item = $map->find($types, Hash::get($target, $c));
            if ($item !== null) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @psalm-assert ItemBind $item
     * @phpstan-assert ItemBind $item
     */
    protected function checkItem(?ItemBind $item): void
    {
        if ($item === null) {
            throw new \RuntimeException('Попытка заменить зависимость которой нету');
        } elseif ($item->status & $item::SWAP) {
            throw new \RuntimeException('Попытка заменить зависимость которая уже заменена');
        }
    }
}
