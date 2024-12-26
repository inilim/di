<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Bind;

final class DI
{
    /** @var Bind */
    protected $bind;

    function __construct(Bind $bind)
    {
        $this->bind = $bind;
    }

    /**
     * @param class-string $abstract
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return object
     */
    function get(string $abstract, $context = null, array $args = [])
    {
        return $this->bind->get($abstract, $context, $args) ?? new $abstract(...$args);
    }
}
