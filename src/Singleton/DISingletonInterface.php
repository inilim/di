<?php

declare(strict_types=1);

namespace Inilim\DI\Singleton;

interface DISingletonInterface
{
    /**
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    public function get(string $abstract): object;

    /**
     * @param class-string $abstract
     * @param \Closure|class-string|null $concrete
     */
    public function bind(string $abstract, $concrete = null): void;
}
