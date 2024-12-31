<?php

namespace Inilim\Test\Other;

class ConcreteSingleton
{
    var $prop1;
    var $prop2;
    var $prop3;
    var $prop4;
    var $prop5;

    function __construct()
    {
        $args = func_get_args();

        $this->prop1 = $args[0] ?? null;
        $this->prop2 = $args[1] ?? null;
        $this->prop3 = $args[2] ?? null;
        $this->prop4 = $args[3] ?? null;
        $this->prop5 = $args[4] ?? null;
    }
}
