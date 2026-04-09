<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
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
        $this->mapInstance->bindOverwrite(Map::T_VALUE_TAG_SWAP, $target, $swap, $context);
        return $this;
    }
}
