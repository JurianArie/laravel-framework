<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Routing\Middleware\ValidateSignature;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Signed extends Middleware
{
    public function __construct(
        ?array $relative = null,
        ?array $absolute = null,
        ?array $only = null,
        ?array $except = null)
    {
        $hasRelative = $relative !== null;
        $hasAbsolute = $absolute !== null;

        $middleware = match (true) {
            $hasRelative && $hasAbsolute => throw new InvalidArgumentException('Cannot specify both relative and absolute URLs for signed middleware.'),
            $hasRelative => ValidateSignature::relative($relative),
            $hasAbsolute => ValidateSignature::absolute($absolute),
            default => 'signed',
        };

        $middleware = str_replace(ValidateSignature::class, 'signed', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
