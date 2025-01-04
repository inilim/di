<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\TestCase;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\Concrete2;

use Inilim\Test\Other\IAbstract;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

/**
 */
class MapTest extends TestCase
{
    function setUp(): void
    {
        self::clearBindMap();
    }

    function test_map()
    {
        Bind::self()->class(IAbstract::class, Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap());
    }

    function test_map2()
    {
        Bind::self()->classIf(IAbstract::class, Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap());
    }

    function test_map3()
    {
        Bind::self()->classIf(Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap());
    }

    function test_map4()
    {
        Bind::self()->singleton(Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON]);
        assertCount(1, self::getMap());
    }

    function test_map5()
    {
        Bind::self()->singletonIf(Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON]);
        assertCount(1, self::getMap());
    }

    function test_map6()
    {
        Bind::self()->classTag('tagName', Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS_TAG]);
        assertCount(1, self::getMap());
    }

    function test_map7()
    {
        Bind::self()->classTag('tagName', Concrete::class);
        Bind::self()->classTag('tagName2', Concrete::class);
        assertCount(2, self::getMap()[Bind::KEY_CLASS_TAG]);
        assertCount(1, self::getMap());
    }

    function test_map8()
    {
        Bind::self()->classTag('tagName', Concrete::class);
        Bind::self()->classTagIf('tagName2', Concrete::class);
        assertCount(2, self::getMap()[Bind::KEY_CLASS_TAG]);
        assertCount(1, self::getMap());
    }

    function test_map9()
    {
        Bind::self()->class(Concrete::class);
        Bind::self()->classIf(Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap());
    }

    function test_map10()
    {
        Bind::self()->singleton(Concrete::class);
        Bind::self()->singletonIf(Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON]);
        assertCount(1, self::getMap());
    }

    function test_map11()
    {
        Bind::self()->singleton(Concrete::class);
        Bind::self()->singletonIf(Concrete::class);
        Bind::self()->singletonTag('tag', Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON]);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON_TAG]);
        assertCount(2, self::getMap());
    }

    function test_map13()
    {
        Bind::self()->singleton(Concrete::class);
        Bind::self()->singletonIf(Concrete::class);
        Bind::self()->singletonTag('tag', Concrete::class);
        Bind::self()->singletonTagIf('tag', Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON]);
        assertCount(1, self::getMap()[Bind::KEY_SINGLETON_TAG]);
        assertCount(2, self::getMap());
    }

    function test_map12()
    {
        Bind::self()->class(Concrete::class);
        Bind::self()->classIf(Concrete::class);
        Bind::self()->classTag('tag', Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap()[Bind::KEY_CLASS_TAG]);
        assertCount(2, self::getMap());
    }

    function test_map14()
    {
        Bind::self()->class(Concrete::class);
        Bind::self()->classIf(Concrete::class);
        Bind::self()->classTag('tag', Concrete::class);
        Bind::self()->classTagIf('tag', Concrete::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap()[Bind::KEY_CLASS_TAG]);
        assertCount(2, self::getMap());
    }

    function test_map15()
    {
        Bind::self()->class(Concrete::class);
        Bind::self()->swap(Concrete::class, Concrete2::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS]);
        assertCount(1, self::getMap()[Bind::KEY_SWAP]);
        assertCount(2, self::getMap());
    }

    function test_map16()
    {
        Bind::self()->classTag('tag', Concrete::class);
        Bind::self()->swapTag('tag', Concrete2::class);
        assertCount(1, self::getMap()[Bind::KEY_CLASS_TAG]);
        assertCount(1, self::getMap()[Bind::KEY_SWAP_TAG]);
        assertCount(2, self::getMap());
    }
}
