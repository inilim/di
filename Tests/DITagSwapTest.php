<?php

use Inilim\DI\Bind;
use Inilim\DI\DI;
use Inilim\DI\Swap;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\ConcreteSwap;
use Inilim\Test\Other\Context;
use Inilim\Test\TestCase;

class DITagSwapTest extends TestCase
{
    function test_without_context(): void
    {
        $name = 'tagName';

        self::clearBindMap();

        Bind::self()->classTag($name, Concrete::class);
        Swap::self()->classTag($name, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name));
        $this->assertNull(DITag('tagNameNull'));
    }

    function test_with_context(): void
    {
        $context = new Context;
        $name = 'tagName';

        self::clearBindMap();

        Bind::self()->classTag($name, Concrete::class, $context);
        Swap::self()->classTag($name, ConcreteSwap::class, $context);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name, $context));
        $this->assertNull(DITag($name));
    }
}
