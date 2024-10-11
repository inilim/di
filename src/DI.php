<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Classic\DIClassic;
use Inilim\DI\Primitive\DIPrimitive;
use Inilim\DI\Singleton\DISingleton;
use Inilim\DI\Swap\DISwap;

final class DI
{
    protected DIClassic $classic;
    protected DIPrimitive $primitive;
    protected DISingleton $singleton;
    protected DISwap $swap;

    function __construct(
        DIClassic $classic,
        DIPrimitive $primitive,
        DISingleton $singleton,
        DISwap $swap
    ) {
        $this->classic   = $classic;
        $this->primitive = $primitive;
        $this->singleton = $singleton;
        $this->swap      = $swap;
    }

    /**
     * @template T of object
     * @param class-string<T> $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @return T
     */
    function __invoke(string $abstract, $context = null, ...$args): object
    {
        return $this->getSwapClass($abstract, $context, ...$args) ?? $this->classic->get($abstract, $context, ...$args);
    }

    /**
     * @param non-empty-string $key
     * @param null|class-string|object $context
     * @param mixed $default
     * @return mixed
     */
    function primitive(string $key, $context = null, $default = null)
    {
        return $this->swap->getPrimitive($key, $context) ?? $this->primitive->get($key, $context, $default);
    }

    /**
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    function singleton(string $abstract): object
    {
        return $this->getSwapClass($abstract) ?? $this->singleton->get($abstract);
    }

    protected function getSwapClass(string $abstract, $context = null, ...$args): ?object
    {
        if (!$this->swap->hasBindClass()) {
            return null;
        }
        return $this->swap->getClass($abstract, $context, ...$args);
    }
}
