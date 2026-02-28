<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as AuthenticateWithBasicAuthMiddleware;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AuthenticateWithBasicAuth extends Middleware
{
    public function __construct(
        ?string $guard = null,
        ?string $field = null,
        ?array $only = null,
        ?array $except = null,
    ) {
        $hasGuard = $guard !== null;
        $hasField = $field !== null;

        $middleware = match (true) {
            $hasGuard && $hasField => AuthenticateWithBasicAuthMiddleware::using(
                guard: $guard,
                field: $field,
            ),
            $hasGuard => AuthenticateWithBasicAuthMiddleware::using(
                guard: $guard,
            ),
            $hasField => AuthenticateWithBasicAuthMiddleware::using(
                field: $field,
            ),
            default => 'auth.basic',
        };
        $middleware = str_replace(AuthenticateWithBasicAuthMiddleware::class, 'auth.basic', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
