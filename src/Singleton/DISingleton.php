<?php

declare(strict_types=1);

namespace Inilim\DI\Singleton;

use Inilim\DI\ItemConcrete;
use Inilim\DI\Singleton\DISingletonInterface;

final class DISingleton implements DISingletonInterface
{
    /**
     * @var null|array<string,object|ItemConcrete>
     */
    protected ?array $bind = null;

    /**
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    function get(string $abstract): object
    {
        $_abstract = \ltrim($abstract, '\\');

        if ($this->bind === null) {
            throw new \Exception(\sprintf(
                'Target [%s] is not instantiable.',
                $_abstract
            ));
        }

        $hash      = \md5($_abstract);
        $obj       =  $this->bind[$hash] ?? null;

        if ($obj === null) {
            throw new \Exception(\sprintf(
                'Target [%s] is not instantiable.',
                $_abstract
            ));
        }

        if (!($obj instanceof ItemConcrete)) {
            return $obj;
        }

        if (!(\class_exists($_abstract, true) || \interface_exists($_abstract, true))) {
            throw new \Exception(\sprintf(
                'class "%s" not found',
                $_abstract
            ));
        }

        $concrete = $obj->getConcrete();
        unset($obj);
        if (\is_string($concrete)) {

            if (!\class_exists($concrete, true)) {
                throw new \Exception(\sprintf(
                    'class "%s" not found',
                    $concrete
                ));
            }

            $concrete = new $concrete;
        } else {
            $concrete = $concrete->__invoke();
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

        return $this->bind[$hash] = $concrete;
    }

    /**
     * @param class-string $abstract
     * @param \Closure|class-string|null $concrete
     */
    function bind(string $abstract, $concrete = null): void
    {
        $_abstract = \ltrim($abstract, '\\');
        if (\is_string($concrete)) {
            $concrete = \ltrim($concrete, '\\');
        }
        $this->bind ??= [];
        $this->bind[\md5($_abstract)] = new ItemConcrete($concrete ?? $_abstract);
    }
}
