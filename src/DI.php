<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Bind;
use Inilim\Singleton\SimpleSingleton;

/**
 * @api
 */
final class DI
{
    use SimpleSingleton;

    /**
     * @var \Closure(string, mixed[]):?object
     */
    protected \Closure $closureBind;

    private function __construct()
    {
        $bind = Bind::self();

        $this->closureBind = (function (string $method, array $args = []) {
             /** @phpstan-ignore method.dynamicName */
            return $this->$method(...$args);
        })->bindTo($bind, $bind);
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
        return $this->getByAbstract($dependence, $context, $args);
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object|mixed[] $argsOrContext array is args else context
     * @param null|class-string|object $context
     */
    function DITag(string $tag, $argsOrContext = null, $context = null): ?object
    {
        [$context, $args] = $this->defineArgsContext($argsOrContext, $context);
        return $this->getByTag($tag, $context, $args);
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

    /**
     * @param class-string $abstract
     * @param null|class-string|object $context
     * @param mixed[] $args
     */
    protected function getByAbstract(string $abstract, $context = null, array $args = []): object
    {
        // @phpstan-ignore-next-line
        return $this->closureBind->__invoke(__FUNCTION__, \func_get_args()) ?? $this->make($abstract, $args);
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object $context
     * @param mixed[] $args
     */
    protected function getByTag(string $tag, $context = null, array $args = []): ?object
    {
        // @phpstan-ignore-next-line
        return $this->closureBind->__invoke(__FUNCTION__, \func_get_args());
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
}
