<?php

namespace Inilim\Test;

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\Dump\Dump;

class TestCase extends \PHPUnit\Framework\TestCase
{
    static function clearBindMap(): void
    {
        Bind::self()->clear();
    }

    static function getMap(): array
    {
        $b = Bind::self();
        return (function () {
            /** @var Bind $this */
            return $this->map;
        })
            ->bindTo($b, $b)
            ->__invoke();
    }

    static function setUpBeforeClass(): void
    {
        Dump::init();
        self::clearBindMap();
    }

    /**
     * Without default make object
     * @template T of object
     * @param class-string<T> $dependence
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    static function DI(string $dependence, $argsOrContext = null, $context = null)
    {
        $args = [];
        if (\is_array($argsOrContext)) {
            $args = $argsOrContext;
        } else {
            $context = $argsOrContext;
        }

        return (function () {
            return $this->closureBind->__invoke('getByAbstract', \func_get_args());
        })
            ->bindTo(DI::self(), DI::self())
            ->__invoke($dependence, $context, $args);
    }
}
