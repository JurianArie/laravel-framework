<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\AuthenticateWithBasicAuth;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class AuthenticateWithBasicAuthMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [AuthenticateWithBasicAuthMiddlewareAttributeController::class, 'index']);
        $this->assertEquals([
            'auth.basic',
            'auth.basic:guard',
            'auth.basic:also-index',
        ], $route->controllerMiddleware());

        $route = Route::get('/', [AuthenticateWithBasicAuthMiddlewareAttributeController::class, 'show']);
        $this->assertEquals($route->controllerMiddleware(), [
            'auth.basic',
            'auth.basic:other-guard,username',
        ]);
    }
}

#[AuthenticateWithBasicAuth]
#[AuthenticateWithBasicAuth('guard', only: ['index'])]
#[AuthenticateWithBasicAuth('other-guard', 'username', except: ['index'])]
class AuthenticateWithBasicAuthMiddlewareAttributeController
{
    #[AuthenticateWithBasicAuth('also-index')]
    public function index(): void
    {
        // ...
    }

    public function show(): void
    {
        // ...
    }
}
