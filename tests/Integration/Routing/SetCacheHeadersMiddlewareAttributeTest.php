<?php

namespace Illuminate\Tests\Integration\Routing;

use Illuminate\Routing\Attributes\Controllers\SetCacheHeaders;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class SetCacheHeadersMiddlewareAttributeTest extends TestCase
{
    public function test_attribute_is_respected(): void
    {
        $route = Route::get('/', [SetCacheHeadersMiddlewareAttributeController::class, 'index']);
        $this->assertEquals($route->controllerMiddleware(), [
            'cache.headers:all',
            'cache.headers:only-index',
            'cache.headers:also-index',
        ]);

        $route = Route::get('/', [SetCacheHeadersMiddlewareAttributeController::class, 'show']);
        $this->assertEquals([
            'cache.headers:all',
            'cache.headers:a;b',
        ], $route->controllerMiddleware());
    }
}

#[SetCacheHeaders('all')]
#[SetCacheHeaders('only-index', only: ['index'])]
#[SetCacheHeaders(['a', 'b'], except: ['index'])]
class SetCacheHeadersMiddlewareAttributeController
{
    #[SetCacheHeaders('also-index')]
    public function index(): void
    {
        // ...
    }

    public function show(): void
    {
        // ...
    }
}
