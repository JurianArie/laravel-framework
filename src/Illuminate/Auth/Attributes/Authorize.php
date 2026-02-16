<?php

namespace Illuminate\Auth\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Authorize
{
    /**
     * Create a new attribute instance.
     *
     * @param  \UnitEnum|string  $ability
     * @param  mixed  $arguments
     */
    public function __construct(
        public $ability,
        public $arguments = []
    ) {
    }
}
