<?php

namespace Inilim\DI;

class PrimitiveItem
{
    public function __construct(
        /**
         * @var mixed
         */
        public readonly mixed $value,
    ) {
    }
}
