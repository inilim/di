<?php

namespace Inilim\DI;

use Inilim\DI\Swap\Bind as SwapBind;
use Inilim\DI\Classic\Bind as ClassicBind;
use Inilim\DI\Primitive\Bind as PrimitiveBind;
use Inilim\DI\Singleton\Bind as SingletonBind;

final class Bind
{
    protected ClassicBind $classic;
    protected PrimitiveBind $primitive;
    protected SingletonBind $singleton;
    protected SwapBind $swap;

    function __construct(
        ClassicBind $classic,
        PrimitiveBind $primitive,
        SingletonBind $singleton,
        SwapBind $swap
    ) {
        $this->classic   = $classic;
        $this->primitive = $primitive;
        $this->singleton = $singleton;
        $this->swap      = $swap;
    }

    /**
     * @param non-empty-string $key
     * @param mixed $swap return value
     * @param null|class-string|class-string[] $context
     */
    function swap(string $key, $swap, $context = null): self
    {
        $this->swap->bind($key, $swap, $context);
        return $this;
    }
}
