<?php

declare(strict_types=1);

namespace Inilim\DI\Primitive;

interface DIPrimitiveInterface
{
    /**
     * @param non-empty-string $key
     * @param null|class-string|object $context
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $context = null, $default = null);

    /**
     * @param non-empty-string $key
     * @param mixed $give return value
     * @param null|class-string|class-string[] $context
     */
    public function bind(string $key, $give, $context = null): void;
}
