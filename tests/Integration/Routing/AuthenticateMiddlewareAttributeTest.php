<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\Authenticate;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class AuthenticateMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [AuthenticateMiddlewareAttributeController::class, 'index']);
        $this->assertEquals([
            'auth:all',
            'auth:only-index,a',
            'auth:also-index',
        ], $route->controllerMiddleware());

        $route = Route::get('/', [AuthenticateMiddlewareAttributeController::class, 'show']);
        $this->assertEquals([
            'auth:all',
            'auth:except-index,a,b',
        ], $route->controllerMiddleware());
    }
}

#[Authenticate('all')]
#[Authenticate(['only-index', 'a'], only: ['index'])]
#[Authenticate(['except-index', 'a', 'b'], except: ['index'])]
class AuthenticateMiddlewareAttributeController
{
    #[Authenticate('also-index')]
    public function index(): void
    {
        // ...
    }

    public function show(): void
    {
        // ...
    }
}
