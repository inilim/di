<?php

declare(strict_types=1);

namespace Inilim\DI\Singleton;

use Inilim\DI\ItemConcrete;

final class Bind
{
    /**
     * @var null|array<string,object|ItemConcrete>
     */
    protected ?array $map = null;

    /**
     * @param class-string $abstract
     * @param \Closure|class-string|null $concrete
     */
    function bind(string $abstract, $concrete = null): void
    {
        $_abstract = \ltrim($abstract, '\\');
        $_concrete = $concrete;

        if (\is_string($_concrete)) {
            $_concrete = \ltrim($_concrete, '\\');
        }
        $this->map ??= [];
        $this->map[\md5($_abstract)] = new ItemConcrete($_concrete ?? $_abstract);
    }

    /**
     * @return null|object|ItemConcrete
     */
    function get(string $hash): ?object
    {
        $this->map ??= [];
        return $this->map[$hash] ?? null;
    }

    function set(string $hash, object $obj): void
    {
        $this->map ??= [];
        $this->map[$hash] = $obj;
    }
}
