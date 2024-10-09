<?php

declare(strict_types=1);

namespace Inilim\DI\Primitive;

use Inilim\DI\Hash;
use Inilim\DI\Primitive\Bind;

final class DIPrimitive
{
    protected Bind $bind;

    function __construct(Bind $bind)
    {
        $this->bind = $bind;
    }

    /**
     * @param non-empty-string $key
     * @param null|class-string|object $context
     * @param mixed $default
     * @return mixed
     */
    function get(string $key, $context = null, $default = null)
    {
        $hash = Hash::getWithContext($key, $context);
        $item = $this->bind->get($hash);

        if ($item === null) {
            return $default;
        }

        return $item->getValue();
    }
}
