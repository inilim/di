<?php

declare(strict_types=1);

namespace Inilim\DI;

/**
 * @psalm-readonly
 * @internal 
 */
final class ItemConcrete
{
    /**
     * @var class-string|\Closure
     */
    protected $concrete;

    /**
     * @param class-string|\Closure $concrete
     */
    function __construct($concrete)
    {
        if (!(\is_string($concrete) || $concrete instanceof \Closure)) {
            throw new \LogicException(\sprintf(
                '$concrete must be of type class-string|\Closure, %s given',
                \gettype($concrete)
            ));
        }

        $this->concrete = $concrete;
    }

    /**
     * @return class-string|\Closure
     */
    function getConcrete()
    {
        return $this->concrete;
    }
}
