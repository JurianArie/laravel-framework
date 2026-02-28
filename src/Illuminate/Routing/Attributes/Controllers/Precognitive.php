<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Precognitive extends Middleware
{
    public function __construct(?array $only = null, ?array $except = null)
    {
        parent::__construct('precognitive', $only, $except);
    }
}
