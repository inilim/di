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
            return $this->$method(...$args);
        })->bindTo($bind, $bind);
    }

    /**
     * @param class-string $abstract
     * @param null|class-string|object $context
     * @param mixed[] $args
     */
    function getByAbstract(string $abstract, $context = null, array $args = []): object
    {
        // @phpstan-ignore-next-line
        return $this->closureBind->__invoke(__FUNCTION__, \func_get_args()) ?? $this->make($abstract, $args);
    }

    /**
     * @param non-empty-string $tag
     * @param null|class-string|object $context
     * @param mixed[] $args
     */
    function getByTag(string $tag, $context = null, array $args = []): ?object
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
