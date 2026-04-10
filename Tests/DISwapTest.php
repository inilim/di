<?php

use Inilim\DI\Bind;
use Inilim\DI\DI;
use Inilim\DI\Swap;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\ConcreteSwap;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\IAbstract;
use Inilim\Test\TestCase;

/**
 */
class DISwapTest extends TestCase
{
    function test_swap_target_double()
    {
        self::clearBindMap();

        Bind::self()
            ->class(Concrete::class);
        $this->expectException(\RuntimeException::class);
        Swap::self()
            ->class(Concrete::class, ConcreteSwap::class)
            ->class(Concrete::class, ConcreteSwap::class);
    }

    function test_swap_target_undefined()
    {
        self::clearBindMap();

        $this->expectException(\RuntimeException::class);
        Swap::self()->class(Concrete::class, ConcreteSwap::class);
    }

    function test_class_string__without_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(Concrete::class);
        Swap::self()->class(Concrete::class, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DI(Concrete::class));
    }

    function test_closure__without_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(Concrete::class, static function () {
            return new Concrete;
        });
        Swap::self()->class(Concrete::class, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DI(Concrete::class));
    }

    function test_class_string__with_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(IAbstract::class, Concrete::class, Context::class);
        Swap::self()->class(IAbstract::class, ConcreteSwap::class, Context::class);

        $context = new Context;
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, \get_class($context)));
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, $context));
    }

    function test_closure__with_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(IAbstract::class, static function () {
            return new Concrete;
        }, Context::class);
        Swap::self()->class(IAbstract::class, static function () {
            return new ConcreteSwap;
        }, Context::class);

        $context = new Context;
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, \get_class($context)));
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, $context));
    }
}
