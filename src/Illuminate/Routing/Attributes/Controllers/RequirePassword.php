<?php

namespace Illuminate\Routing\Attributes\Controllers;

use Attribute;
use Illuminate\Auth\Middleware\RequirePassword as RequirePasswordMiddleware;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RequirePassword extends Middleware
{
    public function __construct(
        ?string $redirectToRoute = null,
        string|int|null $passwordTimeoutSeconds = null,
        ?array $only = null,
        ?array $except = null,
    ) {
        $hasRedirectToRoute = $redirectToRoute !== null;
        $hasPasswordTimeoutSeconds = $passwordTimeoutSeconds !== null;

        $middleware = match (true) {
            $hasRedirectToRoute && $hasPasswordTimeoutSeconds => RequirePasswordMiddleware::using(
                redirectToRoute: $redirectToRoute,
                passwordTimeoutSeconds: $passwordTimeoutSeconds,
            ),
            $hasRedirectToRoute => RequirePasswordMiddleware::using(
                redirectToRoute: $redirectToRoute,
            ),
            $hasPasswordTimeoutSeconds => RequirePasswordMiddleware::using(
                passwordTimeoutSeconds: $passwordTimeoutSeconds,
            ),
            default => 'password.confirm',
        };
        $middleware = str_replace(RequirePasswordMiddleware::class, 'password.confirm', $middleware);

        parent::__construct($middleware, $only, $except);
    }
}
