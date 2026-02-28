<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Auth\Middleware\Authorize as AuthorizeMiddleware;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Authorize extends Middleware
{
    public function __construct(string $ability, mixed $arguments = null, ?array $only = null, ?array $except = null)
    {
        $middleware = AuthorizeMiddleware::using($ability, ...Arr::wrap($arguments));
        $middleware = str_replace(AuthorizeMiddleware::class, 'can', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
