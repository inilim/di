<?php

declare(strict_types=1);

namespace Inilim\DI\Classic;

use Inilim\DI\Hash;
use Inilim\DI\ItemConcrete;

final class Bind
{
    /**
     * @var null|array<string,ItemConcrete>
     */
    protected ?array $map = null;

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure $concrete realization/implementation
     * @param null|class-string|class-string[] $context
     */
    function bind(string $abstract, $concrete = null, $context = null): void
    {
        $_abstract = \ltrim($abstract, '\\');
        $_concrete = $concrete;

        if (\is_string($_concrete)) {
            $_concrete = \ltrim($_concrete, '\\');
        }

        $item = new ItemConcrete($_concrete ?? $_abstract);

        $this->map ??= [];

        $_context  = \is_array($context) ? $context : [$context];
        foreach ($_context as $c) {
            $hash = Hash::getWithContext($_abstract, $c);
            $this->map[$hash] = $item;
        }
    }

    function get(string $hash): ?ItemConcrete
    {
        $this->map ??= [];
        return $this->map[$hash] ?? null;
    }
}
