<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\TestCase;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\Concrete2;

use Inilim\Test\Other\IAbstract;
use function PHPUnit\Framework\assertNotInstanceOf;

/**
 * TODO !!!!
 */
class DITest extends TestCase
{
    // без контекста без абстрации
    function test_bind_class_string__without_context_and_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(Concrete::class);

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class));
        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, Context::class));
        $a = DI(Concrete::class);
        $b = DI(Concrete::class);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        // если не биндим, то создаем как make
        Bind::self()->class(Concrete::class);

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));
        $c = DI(Concrete::class);
        $this->assertNotEquals($args, array_values((array)$c));

        // ---------------------------------------------
        // без bind
        // ---------------------------------------------

        self::clearBindMap();

        // если не биндим, то создаем как make
        // Bind::self()->class(Concrete::class);

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));
        $c = DI(Concrete::class);
        $this->assertNotEquals($args, array_values((array)$c));
    }
    function test_bind_closure__without_context_and_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(Concrete::class, static function ($id, array $args) {
            return new Concrete(...$args);
        });

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class));
        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, Context::class));
        $a = DI(Concrete::class);
        $b = DI(Concrete::class);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(Concrete::class, static function ($di, array $args) {
            return new Concrete(...$args);
        });

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));
        $c = DI(Concrete::class);
        $this->assertNotEquals($args, array_values((array)$c));
    }

    // без контекста с абстрации
    function test_bind_class_string__without_context_with_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(IAbstract::class, Concrete::class);

        $this->assertInstanceOf(Concrete::class, DI(IAbstract::class));
        $a = DI(IAbstract::class);
        $b = DI(IAbstract::class);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(IAbstract::class, Concrete::class);

        $this->assertInstanceOf(Concrete::class, DI(IAbstract::class, $args));
        $a = DI(IAbstract::class, $args);
        $b = DI(IAbstract::class, $args);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));
        $c = DI(IAbstract::class);
        $this->assertNotEquals($args, array_values((array)$c));
    }
    function test_bind_closure__without_context_with_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(IAbstract::class, static function ($di, array $args) {
            return new Concrete(...$args);
        });

        $this->assertInstanceOf(Concrete::class, DI(IAbstract::class));
        $a = DI(IAbstract::class);
        $b = DI(IAbstract::class);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(IAbstract::class, static function ($di, array $args) {
            return new Concrete(...$args);
        });

        $this->assertInstanceOf(Concrete::class, DI(IAbstract::class, $args));
        $a = DI(IAbstract::class, $args);
        $b = DI(IAbstract::class, $args);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));
        $c = DI(IAbstract::class);
        $this->assertNotEquals($args, array_values((array)$c));
    }

    // с контекстом без абстрации
    // function test_bind_class_string__with_context_without_abstract(): void {}
    // function test_bind_closure__with_context_without_abstract(): void {}

    // с контекстом с абстрации
    // function test_bind_class_string__with_context_and_abstract(): void {}
    // function test_bind_closure__with_context_and_abstract(): void {}
}
