<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Authenticate extends Middleware
{
    /**
     * @param  string|array<string>|null  $guard
     */
    public function __construct(string|array|null $guard = null, ?array $only = null, ?array $except = null)
    {
        $middleware = $guard === null || $guard === [] ? 'auth' : AuthenticateMiddleware::using(...Arr::wrap($guard));
        $middleware = str_replace(AuthenticateMiddleware::class, 'auth', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
