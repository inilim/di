<?php

declare(strict_types=1);

namespace Inilim\DI;

use Inilim\DI\Bind;

/**
 * @api
 */
final class DI
{
    /** @var Bind */
    protected $bind;

    function __construct()
    {
        $this->bind = Bind::self();
    }

    /**
     * @param class-string $abstract
     * @param null|class-string|object $context
     * @param mixed[] $args
     * @return object
     */
    function getByAbstract(string $abstract, $context = null, array $args = [])
    {
        return $this->bind->getByAbstract($abstract, $context, $args) ?? new $abstract(...$args);
    }
}
