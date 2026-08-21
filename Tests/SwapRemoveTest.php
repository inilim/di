<?php

use Inilim\DI\Bind;
use Inilim\DI\Swap;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\ConcreteSingleton;
use Inilim\Test\Other\ConcreteSwap;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\IAbstract;
use Inilim\Test\TestCase;

class SwapRemoveTest extends TestCase
{
    // ------------------------------------------------------------------
    // clearAll
    // ------------------------------------------------------------------

    function test_clear_all(): void
    {
        self::clearBindMap();

        Bind::self()
            ->class(Concrete::class)
            ->classTag('tag.class', Concrete::class)
            ->value('tag.value', 'original');

        Swap::self()
            ->class(Concrete::class, ConcreteSwap::class)
            ->classTag('tag.class', ConcreteSwap::class)
            ->value('tag.value', 'swapped');

        $this->assertInstanceOf(ConcreteSwap::class, DI(Concrete::class));
        $this->assertInstanceOf(ConcreteSwap::class, DITag('tag.class'));
        $this->assertSame('swapped', DIVal('tag.value'));

        Swap::self()->clearAll();

        // оригинальные привязки не тронуты
        $this->assertInstanceOf(Concrete::class, self::DI(Concrete::class));
        $this->assertInstanceOf(Concrete::class, DITag('tag.class'));
        $this->assertSame('original', DIVal('tag.value'));
    }

    function test_clear_all_allow_re_swap(): void
    {
        self::clearBindMap();

        Bind::self()->class(Concrete::class);

        Swap::self()
            ->class(Concrete::class, ConcreteSwap::class)
            ->clearAll()
            ->class(Concrete::class, ConcreteSwap::class);

        $this->assertInstanceOf(ConcreteSwap::class, DI(Concrete::class));
    }

    // ------------------------------------------------------------------
    // remove
    // ------------------------------------------------------------------

    function test_remove_class(): void
    {
        self::clearBindMap();

        Bind::self()
            ->class(Concrete::class)
            ->class(IAbstract::class, Concrete::class);

        Swap::self()
            ->class(Concrete::class, ConcreteSwap::class)
            ->class(IAbstract::class, ConcreteSwap::class);

        Swap::self()->removeClass(Concrete::class);

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class));
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class));
    }

    function test_remove_class_tag(): void
    {
        self::clearBindMap();

        Bind::self()
            ->classTag('tag.a', Concrete::class)
            ->classTag('tag.b', Concrete::class);

        Swap::self()
            ->classTag('tag.a', ConcreteSwap::class)
            ->classTag('tag.b', ConcreteSwap::class);

        Swap::self()->removeClassTag('tag.a');

        $this->assertInstanceOf(Concrete::class, DITag('tag.a'));
        $this->assertInstanceOf(ConcreteSwap::class, DITag('tag.b'));
    }

    function test_remove_value(): void
    {
        self::clearBindMap();

        Bind::self()
            ->value('tag.a', 'original.a')
            ->value('tag.b', 'original.b');

        Swap::self()
            ->value('tag.a', 'swapped.a')
            ->value('tag.b', 'swapped.b');

        Swap::self()->removeValue('tag.a');

        $this->assertSame('original.a', DIVal('tag.a'));
        $this->assertSame('swapped.b', DIVal('tag.b'));
    }

    function test_remove_class_with_context(): void
    {
        self::clearBindMap();

        Bind::self()->class(IAbstract::class, Concrete::class, [Context::class, Concrete::class]);

        Swap::self()
            ->class(IAbstract::class, ConcreteSwap::class, Context::class)
            ->class(IAbstract::class, ConcreteSwap::class, Concrete::class);

        Swap::self()->removeClass(IAbstract::class, Context::class);

        $this->assertInstanceOf(Concrete::class, DI(IAbstract::class, Context::class));
        $this->assertInstanceOf(ConcreteSwap::class, DI(IAbstract::class, Concrete::class));
    }

    function test_remove_class_restores_singleton_instance(): void
    {
        self::clearBindMap();

        Bind::self()->classSingleton(ConcreteSingleton::class);

        $before = DI(ConcreteSingleton::class);
        $this->assertSame($before, DI(ConcreteSingleton::class));

        Swap::self()->class(ConcreteSingleton::class, ConcreteSwap::class);
        $this->assertInstanceOf(ConcreteSwap::class, DI(ConcreteSingleton::class));

        Swap::self()->removeClass(ConcreteSingleton::class);

        // тот же инстанс что и до подмены
        $this->assertInstanceOf(ConcreteSingleton::class, DI(ConcreteSingleton::class));
        $this->assertSame($before, DI(ConcreteSingleton::class));
    }

    function test_remove_value_restores_single_value(): void
    {
        self::clearBindMap();

        Bind::self()->valueSingle('tag', static fn() => 'original');
        $this->assertSame('original', DIVal('tag'));

        Swap::self()->value('tag', 'swapped');
        $this->assertSame('swapped', DIVal('tag'));

        Swap::self()->removeValue('tag');
        $this->assertSame('original', DIVal('tag'));
    }

    function test_remove_is_noop_without_swap(): void
    {
        self::clearBindMap();

        Bind::self()
            ->class(Concrete::class)
            ->classTag('tag.a', Concrete::class)
            ->value('tag.v', 'original');

        // цели никогда не подменялись, исключений нет
        Swap::self()
            ->removeClass(Concrete::class)
            ->removeClassTag('tag.a')
            ->removeValue('tag.v');

        Swap::self()
            ->class(Concrete::class, ConcreteSwap::class)
            ->classTag('tag.a', ConcreteSwap::class)
            ->value('tag.v', 'swapped');

        // повторное удаление, исключений нет
        Swap::self()
            ->removeClass(Concrete::class)
            ->removeClass(Concrete::class)
            ->removeClassTag('tag.a')
            ->removeClassTag('tag.a')
            ->removeValue('tag.v')
            ->removeValue('tag.v');

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class));
        $this->assertInstanceOf(Concrete::class, DITag('tag.a'));
        $this->assertSame('original', DIVal('tag.v'));
    }
}
