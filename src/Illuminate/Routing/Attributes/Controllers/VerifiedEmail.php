<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class VerifiedEmail extends Middleware
{
    public function __construct(?string $redirectTo = null, ?array $only = null, ?array $except = null)
    {
        $middleware = $redirectTo !== null ? EnsureEmailIsVerified::redirectTo($redirectTo) : 'verified';
        $middleware = str_replace(EnsureEmailIsVerified::class, 'verified', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
