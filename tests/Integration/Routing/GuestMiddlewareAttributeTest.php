<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\Guest;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class GuestMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [GuestMiddlewareAttributeController::class, 'index']);
        $this->assertEquals([
            'guest:all',
            'guest:only-index,a',
            'guest:also-index',
        ], $route->controllerMiddleware());

        $route = Route::get('/', [GuestMiddlewareAttributeController::class, 'show']);
        $this->assertEquals([
            'guest:all',
            'guest:except-index,a,b',
            'guest',
        ], $route->controllerMiddleware());
    }
}

#[Guest('all')]
#[Guest(['only-index', 'a'], only: ['index'])]
#[Guest(['except-index', 'a', 'b'], except: ['index'])]
class GuestMiddlewareAttributeController
{
    #[Guest('also-index')]
    public function index(): void
    {
        // ...
    }

    #[Guest]
    public function show(): void
    {
        // ...
    }
}
