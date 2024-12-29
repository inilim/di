<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\Context;
use Inilim\Test\Concrete;
use Inilim\Test\TestCase;
use Inilim\Test\IAbstract;

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

        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args));
        $this->assertInstanceOf(Concrete::class, DI(Concrete::class, $args, Context::class));
        $a = DI(Concrete::class, $args);
        $b = DI(Concrete::class, $args);
        $this->assertNotEquals(
            spl_object_hash($a),
            spl_object_hash($b),
        );
        $this->assertEquals($args, array_values((array)$a));
    }
    // function test_bind_closure__without_context_and_abstract(): void {}
    // function test_bind_object__without_context_and_abstract(): void {}

    // без контекста с абстрации
    // function test_bind_class_string__without_context_with_abstract(): void {}
    // function test_bind_closure__without_context_with_abstract(): void {}
    // function test_bind_object__without_context_with_abstract(): void {}

    // с контекстом без абстрации
    // function test_bind_class_string__with_context_without_abstract(): void {}
    // function test_bind_closure__with_context_without_abstract(): void {}
    // function test_bind_object__with_context_without_abstract(): void {}

    // с контекстом с абстрации
    // function test_bind_class_string__with_context_and_abstract(): void {}
    // function test_bind_closure__with_context_and_abstract(): void {}
    // function test_bind_object__with_context_and_abstract(): void {}
}