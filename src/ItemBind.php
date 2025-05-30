<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\DI;
use Inilim\DI\Bind;

/**
 * @internal \Inilim\DI
 */
final class ItemBind
{
    /**
     * @var class-string|non-empty-string
     */
    protected string $abstractOrTag;
    /**
     * @var Bind::KEY_*
     */
    protected string $type;
    /**
     * @var null|class-string|object|\Closure(Bind, mixed[]):object
     */
    protected $concrete;
    protected ?object $resolvedObject = null;
    protected bool $isTag;
    protected bool $isSingleton;

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
            // @phpstan-ignore-next-line
            $this->abstractOrTag = \ltrim($abstractOrTag, '\\');
            // @phpstan-ignore-next-line
            $this->concrete      = $concrete ?? $this->abstractOrTag;
        }
    }

    /**
     * @param mixed[] $args
     */
    function resolveAndGetConcrete(array $args): object
    {
        if ($this->resolvedObject !== null) {
            return $this->resolvedObject;
        }

        if ($this->isSingleton) {
            // @phpstan-ignore-next-line
            $this->resolvedObject = $this->resolve($this->concrete, $args);
            // Обнуляем concrete так как он не нужен в типе sigleton, обьект уже создан
            $this->concrete       = null;
            return $this->resolvedObject;
        } else {
            // @phpstan-ignore-next-line
            return $this->resolve($this->concrete, $args);
        }
    }

    /**
     * @param class-string|object|\Closure(DI $di, mixed[] $args):object $concrete
     * @param mixed[] $args
     */
    protected function resolve($concrete, array $args): object
    {
        if (\is_string($concrete)) {
            return new $concrete(...$args);
        }

        if ($concrete instanceof \Closure) {
            $obj = $concrete->__invoke(DI::self(), $args);
            if (\is_object($obj)) {
                return $obj;
            }
            throw new \LogicException('$concrete must return object');
        }

        return $concrete;
    }
}
