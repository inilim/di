<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\TestCase;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\Concrete2;
use Inilim\Test\Other\IAbstract;
use Inilim\Test\Other\ConcreteSwap;

/**
 */
class DISwapTest extends TestCase
{
    function test_class_string__without_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(Concrete::class);
        Bind::self()->swap(Concrete::class, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DI(Concrete::class));
    }

    function test_closure__without_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(Concrete::class, static function () {
            return new Concrete;
        });
        Bind::self()->swap(Concrete::class, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DI(Concrete::class));
    }

    function test_class_string__with_context_abstract()
    {
        self::clearBindMap();

        Bind::self()->class(IAbstract::class, Concrete::class, Context::class);
        Bind::self()->swap(IAbstract::class, ConcreteSwap::class, Context::class);

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
        Bind::self()->swap(IAbstract::class, static function () {
            return new ConcreteSwap;
        }, Context::class);

        $context = new Context;
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, \get_class($context)));
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, $context));
    }
}
