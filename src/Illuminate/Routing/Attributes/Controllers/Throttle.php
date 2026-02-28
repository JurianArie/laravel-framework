<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Throttle extends Middleware
{
    public function __construct(
        string $name,
        ?array $only = null,
        ?array $except = null,
    ) {
        parent::__construct('throttle:'.$name, $only, $except);
    }
}
