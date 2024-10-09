<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Classic\DIClassic;
use Inilim\DI\Primitive\DIPrimitive;
use Inilim\DI\Singleton\DISingleton;

final class DI
{
    protected DIClassic $classic;
    protected DIPrimitive $primitive;
    protected DISingleton $singleton;

    function __construct(
        DIClassic $classic,
        DIPrimitive $primitive,
        DISingleton $singleton
    ) {
        $this->classic   = $classic;
        $this->primitive = $primitive;
        $this->singleton = $singleton;
    }

    /**
     * @template T of object
     * @param class-string<T> $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @return T
     */
    function __invoke(string $abstract, $context = null, ...$args): object
    {
        return $this->classic->get($abstract, $context, ...$args);
    }

    /**
     * @param non-empty-string $key
     * @param null|class-string|object $context
     * @param mixed $default
     * @return mixed
     */
    function primitive(string $key, $context = null, $default = null)
    {
        return $this->primitive->get($key, $context, $default);
    }

    /**
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    function singleton(string $abstract): object
    {
        return $this->singleton->get($abstract);
    }
}
