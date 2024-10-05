<?php

declare(strict_types=1);

namespace Inilim\DI\Classic;

interface DIClassicInterface
{
    /**
     * @template T of object
     * @param class-string<T> $abstract contract/interface OR realization/implementation
     * @param null|class-string|object $context
     * @return T
     */
    public function get(string $abstract, $context = null, ...$args): object;

    /**
     * @param class-string $abstract contract/interface OR realization/implementation
     * @param null|class-string|\Closure $concrete realization/implementation
     * @param null|class-string|class-string[] $context
     */
    function bind(string $abstract, $concrete = null, $context = null): void;
}
