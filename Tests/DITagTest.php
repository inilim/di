<?php

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Test\Other\Context;
use Inilim\Test\Other\Concrete;
use Inilim\Test\TestCase;

class DITagTest extends TestCase
{
    function test_via_class_string_without_context(): void
    {
        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class);
        $objTag = DITag('tagName');
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        Bind::self()->classTag('tagNameWithArgs', Concrete::class);

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];
        $objTag = DITag('tagNameWithArgs', $args);
        /** @var Concrete $objTag */

        $this->assertEquals($objTag->prop1, 1000);
        $this->assertEquals($objTag->prop2, 'is_string');
        $this->assertEquals($objTag->prop3, 0.2000);
        $this->assertEquals($objTag->prop4, [1, 2, 3]);
        $this->assertEquals($objTag->prop5, $arg5);
    }

    function test_via_closure_without_context(): void
    {
        self::clearBindMap();

        Bind::self()->classTag('tagName', static function (DI $di, array $args) {
            return $di->make(Concrete::class, $args);
        });

        $objTag = DITag('tagName');
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagName', static function (DI $di, array $args) {
            return new Concrete(...$args);
        });

        $objTag = DITag('tagName');
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);

        // ---------------------------------------------
        // с аргументами создавая неявно обьект внутри closure
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagNameWithArgs', static function (DI $di, array $args) {
            return $di->make(Concrete::class, $args);
        });

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];
        $objTag = DITag('tagNameWithArgs', $args);
        /** @var Concrete $objTag */

        $this->assertEquals($objTag->prop1, 1000);
        $this->assertEquals($objTag->prop2, 'is_string');
        $this->assertEquals($objTag->prop3, 0.2000);
        $this->assertEquals($objTag->prop4, [1, 2, 3]);
        $this->assertEquals($objTag->prop5, $arg5);

        // ---------------------------------------------
        // с аргументами создавая явно обьект внутри closure
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagNameWithArgs', static function (DI $di, array $args) {
            return new Concrete(...$args);
        });

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];
        $objTag = DITag('tagNameWithArgs', $args);
        /** @var Concrete $objTag */

        $this->assertEquals($objTag->prop1, 1000);
        $this->assertEquals($objTag->prop2, 'is_string');
        $this->assertEquals($objTag->prop3, 0.2000);
        $this->assertEquals($objTag->prop4, [1, 2, 3]);
        $this->assertEquals($objTag->prop5, $arg5);

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagName1', Concrete::class)
            ->classTag('tagName2', Concrete::class);

        $o1 = DITag('tagName1');
        $o2 = DITag('tagName2');

        $this->assertInstanceOf(Concrete::class, $o1);
        $this->assertInstanceOf(Concrete::class, $o2);
        $this->assertNotEquals(\spl_object_hash($o1), \spl_object_hash($o2));
    }

    function test_via_object_without_context(): void
    {
        self::clearBindMap();

        $objDep = new Concrete;
        Bind::self()->classTag('tagName', $objDep);

        $objTag = DITag('tagName');
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals(\spl_object_hash($objDep), \spl_object_hash($objTag));
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        $objDep = new Concrete;
        Bind::self()->classTag('tagName', $objDep);

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];

        $objTag = DITag('tagName', $args);
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals(\spl_object_hash($objDep), \spl_object_hash($objTag));
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);
    }

    function test_not_bind(): void
    {
        self::clearBindMap();

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];
        $this->assertNull(DITag('tagName', $args));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        $this->assertNull(DITag('tagName2'));
    }

    function test_via_string_class_with_context_string()
    {
        $context = Context::class;
        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class, $context);

        $this->assertNull(DITag('tagName'));
        $this->assertInstanceOf(Concrete::class, DITag('tagName', $context));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class, $context);

        $objTag = DITag('tagName', $context);
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class, $context);

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];
        $objTag = DITag('tagName', $args, $context);
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, 1000);
        $this->assertEquals($objTag->prop2, 'is_string');
        $this->assertEquals($objTag->prop3, 0.2000);
        $this->assertEquals($objTag->prop4, [1, 2, 3]);
        $this->assertEquals($objTag->prop5, $arg5);
    }

    function test_via_string_class_with_context_obj()
    {
        $context = new Context;
        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class, $context);

        $this->assertNull(DITag('tagName'));
        $this->assertInstanceOf(Concrete::class, DITag('tagName', $context));

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class, $context);

        $objTag = DITag('tagName', $context);
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, null);
        $this->assertEquals($objTag->prop2, null);
        $this->assertEquals($objTag->prop3, null);
        $this->assertEquals($objTag->prop4, null);
        $this->assertEquals($objTag->prop5, null);

        // ---------------------------------------------
        // 
        // ---------------------------------------------

        self::clearBindMap();

        Bind::self()->classTag('tagName', Concrete::class, $context);

        $arg5   = new \stdClass;
        $args   = [1000, 'is_string', 0.2000, [1, 2, 3], $arg5];
        $objTag = DITag('tagName', $args, $context);
        /** @var Concrete $objTag */

        $this->assertInstanceOf(Concrete::class, $objTag);
        $this->assertEquals($objTag->prop1, 1000);
        $this->assertEquals($objTag->prop2, 'is_string');
        $this->assertEquals($objTag->prop3, 0.2000);
        $this->assertEquals($objTag->prop4, [1, 2, 3]);
        $this->assertEquals($objTag->prop5, $arg5);
    }
}
