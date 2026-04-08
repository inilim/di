<?php

namespace Inilim\Test;

use Inilim\DI\DI;
use Inilim\DI\Bind;
use Inilim\DI\Map;
use Inilim\Dump\Dump;

class TestCase extends \PHPUnit\Framework\TestCase
{
    static function clearBindMap(): void
    {
        Bind::self()->clear();
    }

    static function getMap(): array
    {
        return Map::self()->map;
    }

    static function setUpBeforeClass(): void
    {
        Dump::init();
        self::clearBindMap();
    }

    /**
     * если обьекта нет возвращаем null, по дефолту мы используем метод "... ?? make(...);"
     * @template T of object
     * @param class-string<T> $dependence
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return ?T
     */
    static function DI(string $dependence, $argsOrContext = null, $context = null): ?object
    {
        $args = [];
        if (\is_array($argsOrContext)) {
            $args = $argsOrContext;
        } else {
            $context = $argsOrContext;
        }

        return Map::self()->getClassByAbstract($dependence, $context, $args);
    }
}
