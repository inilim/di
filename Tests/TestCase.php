<?php

namespace Inilim\Test;

use Inilim\DI\Bind;
use Inilim\Dump\Dump;

class TestCase extends \PHPUnit\Framework\TestCase
{
    static ?\ReflectionProperty $refProp = null;

    static function clearBindMap(): void
    {
        self::$refProp ??= new \ReflectionProperty(Bind::self(), 'map');
        self::$refProp->setValue(Bind::self(), []);
    }

    static function setUpBeforeClass(): void
    {
        Dump::init();
        self::clearBindMap();
    }
}
