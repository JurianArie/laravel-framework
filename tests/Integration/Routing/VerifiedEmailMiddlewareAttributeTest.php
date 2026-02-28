<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\VerifiedEmail;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class VerifiedEmailMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [VerifiedEmailMiddlewareAttributeController::class, 'index']);
        $this->assertEquals([
            'verified',
            'verified:to-route',
            'verified:also-index',
        ], $route->controllerMiddleware());

        $route = Route::get('/', [VerifiedEmailMiddlewareAttributeController::class, 'show']);
        $this->assertEquals([
            'verified',
            'verified:other-route',
        ], $route->controllerMiddleware());
    }
}

#[VerifiedEmail]
#[VerifiedEmail('to-route', only: ['index'])]
#[VerifiedEmail('other-route', except: ['index'])]
class VerifiedEmailMiddlewareAttributeController
{
    #[VerifiedEmail('also-index')]
    public function index(): void
    {
        // ...
    }

    public function show(): void
    {
        // ...
    }
}
