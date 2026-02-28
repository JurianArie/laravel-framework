<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Http\Middleware\SetCacheHeaders as SetCacheHeadersMiddleware;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class SetCacheHeaders extends Middleware
{
    /**
     * @param  string|array  $value
     */
    public function __construct(string|array $value, ?array $only = null, ?array $except = null)
    {
        $middleware = SetCacheHeadersMiddleware::using($value);
        $middleware = str_replace(SetCacheHeadersMiddleware::class, 'cache.headers', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
