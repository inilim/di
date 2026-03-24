<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Map;
use Inilim\Singleton\SimpleSingleton;

/**
 * @api
 */
final class DI
{
    use SimpleSingleton;

    protected Map $mapInstance;

    private function __construct()
    {
        $this->mapInstance = Map::self();
    }

    /**
     * @template T of object
     * @param class-string<T> $dependence
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return T
     */
    function DI(string $dependence, $argsOrContext = null, $context = null): object
    {
        [$context, $args] = $this->defineArgsContext($argsOrContext, $context);
        return $this->mapInstance->getByAbstract($dependence, $context, $args) ?? $this->make($dependence, $args);
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     */
    function tag(string $tag, $argsOrContext = null, $context = null): ?object
    {
        [$context, $args] = $this->defineArgsContext($argsOrContext, $context);
        return $this->mapInstance->getByTag($tag, $context, $args);
    }

    /**
     * @deprecated use DI::tag()
     * 
     * @param non-empty-string $tag
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     */
    function DITag(string $tag, $argsOrContext = null, $context = null): ?object
    {
        return $this->tag($tag, $argsOrContext, $context);
    }

    /**
     * @template T of object
     * @param class-string<T> $dependence
     * @param mixed[] $args
     * @return T
     */
    function make(string $dependence, array $args = []): object
    {
        return new $dependence(...$args);
    }

    /**
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     * @return array{0:null|class-string|object,1:null|mixed[]}
     */
    protected function defineArgsContext($argsOrContext = null, $context = null): array
    {
        $args = [];
        if (\is_array($argsOrContext)) {
            $args = $argsOrContext;
        } elseif ($argsOrContext !== null) {
            $context = $argsOrContext;
        }

        return [$context, $args];
    }
}
