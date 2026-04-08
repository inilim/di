<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Bind;
use Inilim\DI\DI;
use Inilim\DI\Map;

/**
 * @internal \Inilim\DI
 */
final class ItemBind
{
    /** @var mixed */
    protected $concrete;
    /** @var mixed */
    protected $resolvedConcrete;
    protected int $status = 0;
    protected const
        SINGLE   =  1 << 0,
        TAG      =  1 << 1,
        SWAP     =  1 << 2,
        _CLASS   =  1 << 3,
        VALUE    =  1 << 4,
        RESOLVED =  1 << 5
        // 
    ;

    /**
     * @param class-string|non-empty-string $abstractOrTag contract/interface OR realization/implementation OR tag name
     * @param Map::T_* $type
     * @param mixed $concrete
     */
    function __construct(
        string $abstractOrTag,
        int $type,
        $concrete = null
    ) {
        $status = 0;
        $status |= Map::IS_SINGLE & $type ? self::SINGLE : 0;
        $status |= Map::IS_TAG & $type ? self::TAG : 0;
        $status |= Map::IS_SWAP & $type ? self::SWAP : 0;
        $status |= Map::IS_CLASS & $type ? self::_CLASS : 0;
        $status |= Map::IS_VALUE & $type ? self::VALUE : 0;
        $this->status = $status;

        if ($status & self::TAG) {
            $this->concrete = $concrete;
        } elseif ($status & self::_CLASS) {
            // @phpstan-ignore-next-line
            $abstractOrTag = \ltrim($abstractOrTag, '\\');
            // @phpstan-ignore-next-line
            $this->concrete = $concrete ?? $abstractOrTag;
        } else {
            $this->concrete = $concrete;
        }
    }

    /**
     * @param mixed[] $args
     * @return mixed
     */
    function resolveAndGet(array $args)
    {
        if ($this->status & self::RESOLVED) {
            return $this->resolvedConcrete;
        }

        if ($this->status & self::SINGLE) {
            // @phpstan-ignore-next-line
            $this->resolvedConcrete = $this->resolve($this->concrete, $args);
            // Обнуляем concrete так как он не нужен в типе sigleton, обьект или примитив уже реализован
            $this->concrete = null;
            $this->status |= self::RESOLVED;
            return $this->resolvedConcrete;
        } else {
            // реализуем всегда если это не single и это closure
            // @phpstan-ignore-next-line
            return $this->resolve($this->concrete, $args);
        }
    }

    /**
     * @param mixed $concrete
     * @param mixed[] $args
     * @return mixed
     */
    protected function resolve($concrete, array $args)
    {
        if ($this->status & self::_CLASS) {
            /** @var class-string|object|\Closure(DI $di, mixed[] $args):object $concrete */
            if (\is_string($concrete)) {
                return new $concrete(...$args);
            }

            if ($concrete instanceof \Closure) {
                $obj = $concrete(DI::self(), $args);
                if (\is_object($obj)) {
                    return $obj;
                }
                throw new \LogicException('$concrete callback must return object');
            }

            return $concrete;
        }
        // 
        elseif ($this->status & self::VALUE) {
            if ($concrete instanceof \Closure) {
                return $concrete(DI::self(), $args);
            }
            if ($args !== []) {
                throw new \LogicException('TODO');
            }
            return $concrete;
        }
    }
}
