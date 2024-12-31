<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\ConcreteSwap;
use Inilim\Test\TestCase;

class DITagSwapTest extends TestCase
{
    function test_without_context(): void
    {
        self::clearBindMap();

        $name = 'tagName';

        Bind::self()->classTag($name, Concrete::class);
        Bind::self()->swapTag($name, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name));
        $this->assertNull(DITag('tagNameNull'));
    }

    function test_with_context(): void
    {
        $context = new Context;
        self::clearBindMap();

        $name = 'tagName';

        Bind::self()->classTag($name, Concrete::class, $context);
        Bind::self()->swapTag($name, ConcreteSwap::class, $context);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name, $context));
        $this->assertNull(DITag($name));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        $context = Context::class;
        self::clearBindMap();

        $name = 'tagName';

        Bind::self()->classTag($name, Concrete::class, $context);
        Bind::self()->swapTag($name, ConcreteSwap::class, $context);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name, $context));
        $this->assertNull(DITag($name));
    }
}
