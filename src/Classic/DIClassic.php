<?php

declare(strict_types=1);

namespace Inilim\DI\Classic;

use Inilim\DI\Hash;
use Inilim\DI\ItemConcrete;
use Inilim\DI\Classic\DIClassicInterface;

final class DIClassic implements DIClassicInterface
{
    /**
     * @var null|array<string,ItemConcrete>
     */
    protected ?array $bind = null;

    /**
     * @template T of object
     * @param class-string<T> $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @return T
     */
    function get(string $abstract, $context = null, ...$args): object
    {
        $_abstract = \ltrim($abstract, '\\');

        if ($this->bind === null) {
            throw new \Exception(\sprintf(
                'Target [%s] is not instantiable.',
                $_abstract
            ));
        }

        $hash      = Hash::getWithContext($_abstract, $context);
        $item      = $this->bind[$hash] ?? null;

        if ($item === null) {
            throw new \Exception(\sprintf(
                'Target [%s] is not instantiable.',
                $_abstract
            ));
        }

        if (!(\class_exists($_abstract, true) || \interface_exists($_abstract, true))) {
            throw new \Exception(\sprintf(
                'class "%s" not found',
                $_abstract
            ));
        }

        $concrete = $item->getConcrete();
        unset($item);
        if (\is_string($concrete)) {

            if (!\class_exists($concrete, true)) {
                throw new \Exception(\sprintf(
                    'class "%s" not found',
                    $concrete
                ));
            }

            $concrete = new $concrete(...$args);
        } else {
            $concrete = $concrete->__invoke(...$args);
        }


        if (!\is_object($concrete)) {
            throw new \TypeError(\sprintf(
                '$concrete must be of type object<%s>, %s given',
                $_abstract,
                \gettype($concrete)
            ));
        }

        if (!($concrete instanceof $_abstract)) {
            throw new \TypeError(\sprintf(
                '$concrete must be of type "%s", "%s" given',
                $_abstract,
                \get_class($concrete)
            ));
        }

        return $concrete;
    }

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure $concrete realization/implementation
     * @param null|class-string|class-string[] $context
     */
    function bind(string $abstract, $concrete = null, $context = null): void
    {
        $_abstract = \ltrim($abstract, '\\');
        $_context  = \is_array($context) ? $context : [$context];
        if (\is_string($concrete)) {
            $concrete = \ltrim($concrete, '\\');
        }
        $item      = new ItemConcrete($concrete ?? $_abstract);

        $this->bind ??= [];

        foreach ($_context as $c) {
            $hash = Hash::getWithContext($_abstract, $c);
            $this->bind[$hash] = $item;
        }
    }
}
