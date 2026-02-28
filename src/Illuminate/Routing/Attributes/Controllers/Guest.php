<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Guest extends Middleware
{
    /**
     * @param  string|array<string>|null  $guard
     */
    public function __construct(string|array|null $guard = null, ?array $only = null, ?array $except = null)
    {
        $middleware = $guard === null || $guard === [] ? 'guest' : RedirectIfAuthenticated::using(...Arr::wrap($guard));
        $middleware = str_replace(RedirectIfAuthenticated::class, 'guest', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
