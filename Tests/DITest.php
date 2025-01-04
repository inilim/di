<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\TestCase;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\Other\Concrete2;

use Inilim\Test\Other\IAbstract;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNull;

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

        assertInstanceOf(Concrete::class, DI(Concrete::class));
        assertInstanceOf(Concrete::class, DI(Concrete::class, Context::class));
        $a = DI(Concrete::class);
        $b = DI(Concrete::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        // если не биндим, то создаем как make
        Bind::self()->class(Concrete::class);

        assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(Concrete::class);
        assertNotEquals($args, array_values((array)$c));

        // ---------------------------------------------
        // без bind
        // ---------------------------------------------

        self::clearBindMap();

        // если не биндим, то создаем как make
        // Bind::self()->class(Concrete::class);

        assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(Concrete::class);
        assertNotEquals($args, array_values((array)$c));
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

        assertInstanceOf(Concrete::class, DI(Concrete::class));
        assertInstanceOf(Concrete::class, DI(Concrete::class, Context::class));
        $a = DI(Concrete::class);
        $b = DI(Concrete::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(Concrete::class, static function ($di, array $args) {
            return new Concrete(...$args);
        });

        assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(Concrete::class);
        assertNotEquals($args, array_values((array)$c));
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

        assertInstanceOf(Concrete::class, DI(IAbstract::class));
        $a = DI(IAbstract::class);
        $b = DI(IAbstract::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(IAbstract::class, Concrete::class);

        assertInstanceOf(Concrete::class, DI(IAbstract::class, $args));
        $a = DI(IAbstract::class, $args);
        $b = DI(IAbstract::class, $args);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(IAbstract::class);
        assertNotEquals($args, array_values((array)$c));
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

        assertInstanceOf(Concrete::class, DI(IAbstract::class));
        $a = DI(IAbstract::class);
        $b = DI(IAbstract::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(IAbstract::class, static function ($di, array $args) {
            return new Concrete(...$args);
        });

        assertInstanceOf(Concrete::class, DI(IAbstract::class, $args));
        $a = DI(IAbstract::class, $args);
        $b = DI(IAbstract::class, $args);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(IAbstract::class);
        assertNotEquals($args, array_values((array)$c));
    }

    // с контекстом без абстрации
    function test_bind_class_string__with_context_without_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(Concrete::class, null, Context::class);

        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, self::DI(Concrete::class, Context::class));
        $a = self::DI(Concrete::class, Context::class);
        $b = self::DI(Concrete::class, Context::class);
        assertNotEquals(
            \spl_object_hash($a),
            \spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(Concrete::class, null, Context::class);

        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, self::DI(Concrete::class, Context::class));
        $a = self::DI(Concrete::class, $args, Context::class);
        $b = self::DI(Concrete::class, Context::class);
        assertNotEquals(
            \spl_object_hash($a),
            \spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = self::DI(Concrete::class, Context::class);
        assertNotEquals($args, array_values((array)$c));
    }
    function test_bind_closure__with_context_without_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(Concrete::class, static function ($di, $args) {
            return new Concrete(...$args);
        }, Context::class);

        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, self::DI(Concrete::class, Context::class));
        $a = self::DI(Concrete::class, Context::class);
        $b = self::DI(Concrete::class, Context::class);
        assertNotEquals(
            \spl_object_hash($a),
            \spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(Concrete::class, static function ($di, $args) {
            return new Concrete(...$args);
        }, Context::class);

        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, self::DI(Concrete::class, Context::class));
        $a = self::DI(Concrete::class, $args, Context::class);
        $b = self::DI(Concrete::class, Context::class);
        assertNotEquals(
            \spl_object_hash($a),
            \spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = self::DI(Concrete::class, Context::class);
        assertNotEquals($args, array_values((array)$c));
    }

    // с контекстом с абстрации
    function test_bind_class_string__with_context_and_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(IAbstract::class, Concrete::class, Context::class);

        $context = new Context;

        assertNull(self::DI(IAbstract::class));
        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, $context));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, Context::class));
        $a = DI(IAbstract::class, $context);
        $b = DI(IAbstract::class, Context::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(IAbstract::class, Concrete::class, Context::class);

        $context = new Context;
        assertNull(self::DI(IAbstract::class));
        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, $args, $context));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, $args, Context::class));
        $a = DI(IAbstract::class, $args, $context);
        $b = DI(IAbstract::class, $args, Context::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(IAbstract::class, Context::class);
        assertNotEquals($args, array_values((array)$c));
    }
    function test_bind_closure__with_context_and_abstract(): void
    {
        // ---------------------------------------------
        // без аргументов
        // ---------------------------------------------

        self::clearBindMap();

        $args = [null, null, null, null, null];
        Bind::self()->class(IAbstract::class, static function ($di, $args) {
            return new Concrete(...$args);
        }, Context::class);

        $context = new Context;

        assertNull(self::DI(IAbstract::class));
        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, $context));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, Context::class));
        $a = DI(IAbstract::class, $context);
        $b = DI(IAbstract::class, Context::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));

        // ---------------------------------------------
        // с аргументами
        // ---------------------------------------------

        self::clearBindMap();

        $arg5 = new \stdClass;
        $args = [1000, 'string', 0.2000, [1, 2, 3], $arg5];

        Bind::self()->class(IAbstract::class, static function ($di, $args) {
            return new Concrete(...$args);
        }, Context::class);

        $context = new Context;
        assertNull(self::DI(IAbstract::class));
        assertNull(self::DI(Concrete::class));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, $args, $context));
        assertInstanceOf(Concrete::class, DI(IAbstract::class, $args, Context::class));
        $a = DI(IAbstract::class, $args, $context);
        $b = DI(IAbstract::class, $args, Context::class);
        assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        assertEquals($args, array_values((array)$a));
        $c = DI(IAbstract::class, Context::class);
        assertNotEquals($args, array_values((array)$c));
    }
}
