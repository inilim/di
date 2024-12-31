<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\ConcreteSingleton;
use Inilim\Test\Other\ConcreteSwap;
use Inilim\Test\TestCase;

class DISingletonTagSwapTest extends TestCase
{
    function test_without_context(): void
    {
        $name = 'tagName';
        self::clearBindMap();

        Bind::self()->singletonTag($name, ConcreteSingleton::class);
        Bind::self()->swapTag($name, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name));
        $this->assertEquals(
            spl_object_hash(DITag($name)),
            spl_object_hash(DITag($name))
        );
        $this->assertNull(DITag($name, Context::class));
        $this->assertNull(DITag($name, new Context));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->singletonTag($name, static function () {
            return new ConcreteSingleton;
        });
        Bind::self()->swapTag($name, static function () {
            return new ConcreteSwap;
        });

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name));
        $this->assertEquals(
            spl_object_hash(DITag($name)),
            spl_object_hash(DITag($name))
        );
        $this->assertNull(DITag($name, Context::class));
        $this->assertNull(DITag($name, new Context));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->singletonTag($name, static function () {
            return new ConcreteSingleton;
        });
        Bind::self()->swapTag($name, new ConcreteSwap);

        $this->assertInstanceOf(ConcreteSwap::class, DITag($name));
        $this->assertEquals(
            spl_object_hash(DITag($name)),
            spl_object_hash(DITag($name))
        );
        $this->assertNull(DITag($name, Context::class));
        $this->assertNull(DITag($name, new Context));
    }

    function test_with_context(): void
    {
        $contextStr = Context::class;
        $contextObj = new Context;
        $name = 'tagName';

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();


        Bind::self()->singletonTag($name, ConcreteSingleton::class, $contextStr);

        $this->assertInstanceOf(ConcreteSingleton::class, DITag($name, $contextStr));
        $this->assertEquals(
            spl_object_hash(DITag($name, $contextStr)),
            spl_object_hash(DITag($name, $contextStr))
        );

        $this->assertInstanceOf(ConcreteSingleton::class, DITag($name, $contextObj));
        $this->assertEquals(
            spl_object_hash(DITag($name, $contextObj)),
            spl_object_hash(DITag($name, $contextObj))
        );


        $this->assertNull(DITag($name));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();


        Bind::self()->singletonTag($name, static function () {
            return new ConcreteSingleton;
        }, $contextStr);

        $this->assertInstanceOf(ConcreteSingleton::class, DITag($name, $contextStr));
        $this->assertEquals(
            spl_object_hash(DITag($name, $contextStr)),
            spl_object_hash(DITag($name, $contextStr))
        );

        $this->assertInstanceOf(ConcreteSingleton::class, DITag($name, $contextObj));
        $this->assertEquals(
            spl_object_hash(DITag($name, $contextObj)),
            spl_object_hash(DITag($name, $contextObj))
        );


        $this->assertNull(DITag($name));
    }
}
