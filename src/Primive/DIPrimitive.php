<?php

declare(strict_types=1);

namespace Inilim\DI\Primitive;

use Inilim\DI\Hash;
use Inilim\DI\Primitive\Item;
use Inilim\DI\Primitive\DIPrimitiveInterface;

final class DIPrimitive implements DIPrimitiveInterface
{
    /**
     * @var null|array<string,Item>
     */
    protected ?array $bind = null;

    /**
     * @param non-empty-string $key
     * @param null|class-string|object $context
     * @param mixed $default
     * @return mixed
     */
    function get(string $key, $context = null, $default = null)
    {
        if ($this->bind === null) {
            return $default;
        }

        $hash = Hash::getWithContext($key, $context);

        $item = $this->bind[$hash] ?? null;

        if ($item === null) {
            return $default;
        }

        return $item->getValue();
    }

    /**
     * @param non-empty-string $key
     * @param mixed $give return value
     * @param null|class-string|class-string[] $context
     */
    function bind(string $key, $give, $context = null): void
    {
        $_context  = \is_array($context) ? $context : [$context];

        $this->bind ??= [];

        $item = new Item($give);

        foreach ($_context as $c) {
            $hash = Hash::getWithContext($key, $c);
            $this->bind[$hash] = $item;
        }
    }
}
