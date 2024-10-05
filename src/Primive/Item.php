<?php

namespace Inilim\DI\Primitive;

/**
 * @psalm-readonly
 */
final class Item
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    function getValue()
    {
        return $this->value;
    }
}
