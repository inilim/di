<?php

namespace Inilim\DI;

class BindItem
{
    public function __construct(
        /**
         * @var \Closure|class-string
         */
        public readonly \Closure|string $give,
        public readonly bool $is_singleton,
    ) {
    }
}
