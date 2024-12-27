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

    /** @var Bind */
    protected $bind;

    private function __construct()
    {
        $this->bind = Bind::self();
    }

    /**
     * @param class-string $abstract
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return object
     */
    function getByAbstract(string $abstract, $context = null, array $args = [])
    {
        return $this->bind->getByAbstract($abstract, $context, $args) ?? $this->make($abstract, $args);
    }

    /**
     * @param non-empty-string $abstract
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return ?object
     */
    function getByTag(string $tag, $context = null, array $args = [])
    {
        return $this->bind->getByTag($tag, $context, $args);
    }

    /**
     * @template T of object
     * @param class-string $dep
     * @param null|mixed[] $args array is args else context
     * @return T
     */
    function make(string $dep, ?array $args = null)
    {
        return new $dep(...$args);
    }
}
