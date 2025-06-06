<?php

require_once '../vendor/autoload.php';

use Inilim\DI\Bind;
use Inilim\DI\DI;
use Inilim\Dump\Dump;

Dump::init();

Bind::self()->singleton(Dep1::class, static function ($di, $a) {
    $di;
});

class Dep1
{
    function method1() {}
    function method2() {}
}

final class TestClass
{
    protected $dep1;

    function __construct()
    {
        $this->dep1 = \DI(Dep1::class);
    }
}
