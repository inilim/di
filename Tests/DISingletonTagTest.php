<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\Context;
use Inilim\Test\Concrete;
use Inilim\Test\ConcreteSingleton;
use Inilim\Test\ConcreteSwap;
use Inilim\Test\TestCase;

class DISingletonTagTest extends TestCase
{
    function test_without_context(): void
    {
        $name = 'tagName';
        self::clearBindMap();

        Bind::self()->singletonTag($name, ConcreteSingleton::class);

        $this->assertInstanceOf(ConcreteSingleton::class, DITag($name));
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

        $this->assertInstanceOf(ConcreteSingleton::class, DITag($name));
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