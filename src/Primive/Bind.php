<?php

declare(strict_types=1);

namespace Inilim\DI\Primitive;

use Inilim\DI\Hash;
use Inilim\DI\Primitive\Item;

final class Bind
{
    /**
     * @var null|array<string,Item>
     */
    protected ?array $map = null;

    /**
     * @param non-empty-string $key
     * @param mixed $give return value
     * @param null|class-string|class-string[] $context
     */
    function bind(string $key, $give, $context = null): void
    {
        $this->map ??= [];

        $item = new Item($give);

        $_context  = \is_array($context) ? $context : [$context];
        foreach ($_context as $c) {
            $hash = Hash::getWithContext($key, $c);
            $this->map[$hash] = $item;
        }
    }

    function get(string $hash): ?Item
    {
        $this->map ??= [];
        return $this->map[$hash] ?? null;
    }
}
