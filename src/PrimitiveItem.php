<?php

namespace Inilim\DI;

class PrimitiveItem
{
    function __construct(
        /**
         * @var mixed
         */
        public readonly mixed $value,
    ) {
    }
}
