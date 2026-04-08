<?php

use Inilim\DI\Bind;
use Inilim\Test\TestCase;

class DIValueTest extends TestCase
{
    function test_single_if()
    {
        $bind = Bind::self();
        $bind->clear();

        $bind->valueSingleIf('tag', 'foo');
        $bind->valueSingleIf('tag', 'bar');
        $value1 = \DIVal('tag');
        $this->assertSame('foo', $value1);
    }

    function test_single()
    {
        $bind = Bind::self();
        $bind->clear();

        $static = 'foo';
        $bind->valueSingle('tag', static function () use (&$static) {
            return $static;
        });
        $value1 = \DIVal('tag');
        $static = 'bar';
        $value2 = \DIVal('tag');
        $this->assertSame($value1, $value2);
    }


    function test_if()
    {
        $bind = Bind::self();
        $bind->clear();

        $bind->valueIf('tag', 'foo');
        $bind->valueIf('tag', 'bar');

        $this->assertSame('foo', \DIVal('tag'));
    }

    function test_closure_value_with_args()
    {
        $bind = Bind::self();
        $bind->clear();

        $bind->value('id', static function ($di, $args): int {
            return \mt_rand(...$args);
        });

        $value1 = \DIVal('id', [1, 10]);
        $value2 = \DIVal('id', [20, 30]);

        $this->assertTrue(\is_int($value1));
        $this->assertTrue(\is_int($value2));
        $this->assertTrue($value1 !== $value2);
        $this->assertTrue($value1 >= 1 && $value1 <= 10);
        $this->assertTrue($value2 >= 20 && $value2 <= 30);
    }

    function test_closure()
    {
        $bind = Bind::self();
        $bind->clear();

        $bind->value('id', static function (): string {
            return \uniqid();
        });

        $value1 = \DIVal('id');
        $value2 = \DIVal('id');

        $this->assertTrue(\is_string($value1));
        $this->assertTrue(\is_string($value2));
        $this->assertTrue($value1 !== $value2);
    }

    function test_primitive_values()
    {
        $bind = Bind::self();
        $bind->clear();

        $bind
            ->value('string', $string = 'foobar')
            ->value('int', $int = 123)
            ->value('float', $float = 123.123)
            ->value('array', $array = [1, 2, 3])
            ->value('object', $object = new \stdClass)
            ->value('null', $null = null)
            ->value('true', $true = true)
            ->value('false', $false = false)
            ->value('resource', $resource = \fopen('php://temp', 'r+'))
            // 
        ;

        $this->assertSame($string, \DIVal('string'));
        $this->assertSame($int, \DIVal('int'));
        $this->assertSame($float, \DIVal('float'));
        $this->assertSame($array, \DIVal('array'));
        $this->assertSame($object, \DIVal('object'));
        $this->assertSame($null, \DIVal('null'));
        $this->assertSame($true, \DIVal('true'));
        $this->assertSame($false, \DIVal('false'));
        $this->assertSame($resource, \DIVal('resource'));

        \fclose($resource);
    }
}
