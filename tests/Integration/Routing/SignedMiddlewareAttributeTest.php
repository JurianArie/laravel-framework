<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\Signed;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class SignedMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [SignedMiddlewareAttributeController::class, 'index']);
        $this->assertEquals([
            'signed',
            'signed:relative,relative-param',
        ], $route->controllerMiddleware());

        $route = Route::get('/', [SignedMiddlewareAttributeController::class, 'show']);
        $this->assertEquals([
            'signed',
            'signed:absolute-param',
            'signed:relative',
            'signed',
        ], $route->controllerMiddleware());
    }
}

#[Signed]
#[Signed(relative: ['relative-param'], only: ['index'])]
#[Signed(absolute: ['absolute-param'], except: ['index'])]
class SignedMiddlewareAttributeController
{
    public function index(): void
    {
        // ...
    }

    #[Signed(relative: [])]
    #[Signed(absolute: [])]
    public function show(): void
    {
        // ...
    }
}
