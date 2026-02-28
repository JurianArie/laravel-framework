<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\RequirePassword;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class RequirePasswordMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [RequirePasswordMiddlewareAttributeController::class, 'index']);
        $this->assertEquals([
            'password.confirm',
            'password.confirm:to-route',
            'password.confirm:also-index',
        ], $route->controllerMiddleware());

        $route = Route::get('/', [RequirePasswordMiddlewareAttributeController::class, 'show']);
        $this->assertEquals([
            'password.confirm',
            'password.confirm:other-route,500',
        ], $route->controllerMiddleware());
    }
}

#[RequirePassword]
#[RequirePassword('to-route', only: ['index'])]
#[RequirePassword('other-route', 500, except: ['index'])]
class RequirePasswordMiddlewareAttributeController
{
    #[RequirePassword('also-index')]
    public function index(): void
    {
        // ...
    }

    public function show(): void
    {
        // ...
    }
}
