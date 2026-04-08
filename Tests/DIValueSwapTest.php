<?php

use Inilim\DI\Bind;
use Inilim\DI\Swap;
use Inilim\Test\TestCase;

class DIValueSwapTest extends TestCase
{
    function test_single_if()
    {
        $bind = Bind::self();
        $swap = Swap::self();
        $bind->clear();

        $bind->valueSingleIf('tag', static fn() => 'foo');
        $bind->valueSingleIf('tag', static fn() => 'foofoo');

        $swap->value('tag', 'bar');

        $this->assertSame('bar', \DIVal('tag'));
        $this->assertSame('bar', \DIVal('tag'));
    }

    function test_single()
    {
        $bind = Bind::self();
        $swap = Swap::self();
        $bind->clear();

        $bind->valueSingle('tag', static fn() => 'foo');

        $swap->value('tag', 'bar');

        $this->assertSame('bar', \DIVal('tag'));
        $this->assertSame('bar', \DIVal('tag'));
    }

    function test_value_if()
    {
        $bind = Bind::self();
        $swap = Swap::self();
        $bind->clear();

        $bind->valueIf('tag', 'foo');

        $swap->value('tag', 'bar');

        $this->assertSame('bar', \DIVal('tag'));
        $this->assertSame('bar', \DIVal('tag'));
    }

    function test_value()
    {
        $bind = Bind::self();
        $swap = Swap::self();
        $bind->clear();

        $bind->value('tag', 'foo');

        $swap->value('tag', 'bar');

        $this->assertSame('bar', \DIVal('tag'));
        $this->assertSame('bar', \DIVal('tag'));
    }
}
