<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\DI\Bind;

/**
 * @internal \Inilim\DI
 * @psalm-internal \Inilim\DI
 */
final class ItemBind
{
    /**
     * @var class-string|non-empty-string
     */
    protected $abstractOrTag;
    /**
     * @var Bind::KEY_*
     */
    protected $type;
    /**
     * @var null|class-string|object|\Closure(Bind, mixed[]):object
     */
    protected $concrete;
    /**
     * @var ?object
     */
    protected $resolvedObject = null;
    /** 
     * @var bool
     */
    protected $isTag;
    /** 
     * @var bool
     */
    protected $isSingleton;

    /**
     * @param class-string|non-empty-string $abstractOrTag contract/interface OR realization/implementation OR tag name
     * @param Bind::KEY_* $type
     * @param null|class-string|object|\Closure(Bind, mixed[]):object $concrete
     */
    function __construct(
        string $abstractOrTag,
        string $type,
        $concrete = null
    ) {
        $this->type        = $type;
        $this->isTag       = !\in_array($type, [BIND::KEY_CLASS, BIND::KEY_SINGLETON, BIND::KEY_SWAP], true);
        $this->isSingleton = \in_array($type, [BIND::KEY_SINGLETON, BIND::KEY_SINGLETON_TAG], true);

        if ($this->isTag) {
            if ($concrete === null) {
                throw new \InvalidArgumentException('Tag bind not found concrete');
            }
            $this->abstractOrTag = $abstractOrTag;
            $this->concrete      = $concrete;
        } else {
            $this->abstractOrTag = \ltrim($abstractOrTag, '\\');
            $this->concrete      = $concrete ?? $this->abstractOrTag;
        }
    }

    /**
     * @param mixed[] $args
     * @return object
     */
    function resolveAndGetConcrete(array $args)
    {
        if ($this->resolvedObject !== null) return $this->resolvedObject;

        if ($this->isSingleton) {
            $this->resolvedObject = $this->resolve($this->concrete, $args);
            // Обнуляем concrete так как он не нужен в типе sigleton, обьект уже создан
            $this->concrete       = null;
            return $this->resolvedObject;
        } else {
            return $this->resolve($this->concrete, $args);
        }
    }

    /**
     * @param class-string|object|\Closure(DI, mixed[]):object $concrete
     * @param mixed[] $args
     * @return object
     */
    protected function resolve($concrete, array $args)
    {
        if (\is_string($concrete)) {
            return new $concrete(...$args);
        }

        if ($concrete instanceof \Closure) {
            return $concrete->__invoke(DI::self(), $args);
        }

        return $concrete;
    }
}
