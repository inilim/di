<?php

namespace Inilim\DI;

class BindItem
{
    function __construct(
        /**
         * @var \Closure|class-string
         */
        public readonly \Closure|string $concrete,
    ) {
    }
}
