<?php

namespace Illuminate\Tests\Routing;

use Illuminate\Auth\Attributes\Authorize;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;

class AuthorizeAttributeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Container::setInstance(new Container);
    }

    protected function tearDown(): void
    {
        Container::getInstance()->flush();
        parent::tearDown();
    }

    public function test_authorize_attribute_calls_gate_with_ability_only(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with('viewAny', [])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeController;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $dispatcher->dispatch($route, $controller, 'index');
    }

    public function test_authorize_attribute_calls_gate_with_static_arguments(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with('create', ['TestClass'])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeControllerWithArgs;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $dispatcher->dispatch($route, $controller, 'store');
    }

    public function test_authorize_attribute_resolves_dynamic_arguments_from_route_parameters(): void
    {
        $testModel = new TestModel;

        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with('update', [$testModel])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeControllerWithDynamicArgs;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn(['post' => $testModel]);
        $route->method('parametersWithoutNulls')->willReturn(['post' => $testModel]);

        $dispatcher->dispatch($route, $controller, 'update');
    }

    public function test_authorize_attribute_throws_exception_for_missing_parameter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing parameter [post] for authorization.');

        $gate = $this->createMock(Gate::class);
        $gate->expects($this->never())->method('authorize');

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeControllerWithDynamicArgs;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $dispatcher->dispatch($route, $controller, 'update');
    }

    public function test_authorize_attribute_with_multiple_attributes_calls_gate_multiple_times(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->exactly(2))
            ->method('authorize')
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeControllerMultiple;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $dispatcher->dispatch($route, $controller, 'destroy');
    }

    public function test_no_authorize_attribute_does_not_call_gate(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->never())->method('authorize');

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestControllerWithoutAuthorize;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $dispatcher->dispatch($route, $controller, 'index');
    }

    public function test_method_does_not_exist_authorize_is_not_called(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->never())->method('authorize');

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestControllerWithCallMethod;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $this->expectException(\BadMethodCallException::class);

        $dispatcher->dispatch($route, $controller, 'nonExistentMethod');
    }

    public function test_dispatch_calls_call_action_when_method_exists(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with('viewAny', [])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestControllerWithCallAction;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $result = $dispatcher->dispatch($route, $controller, 'index');

        $this->assertSame('callAction result', $result);
    }

    public function test_dispatch_calls_controller_method_directly_when_no_call_action(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with('viewAny', [])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeController;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $result = $dispatcher->dispatch($route, $controller, 'index');

        $this->assertSame('index result', $result);
    }

    public function test_authorize_attribute_works_with_enum_ability(): void
    {
        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with(TestAbility::ViewAny, [])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeControllerWithEnum;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn([]);
        $route->method('parametersWithoutNulls')->willReturn([]);

        $dispatcher->dispatch($route, $controller, 'index');
    }

    public function test_authorize_attribute_with_mixed_arguments(): void
    {
        $testModel = new TestModel;

        $gate = $this->createMock(Gate::class);
        $gate->expects($this->once())
            ->method('authorize')
            ->with('edit', [$testModel, 'admin'])
            ->willReturn(null);

        Container::getInstance()->instance(Gate::class, $gate);

        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestAuthorizeControllerWithMixedArgs;
        $route = $this->createMock(Route::class);
        $route->method('parameters')->willReturn(['post' => $testModel]);
        $route->method('parametersWithoutNulls')->willReturn(['post' => $testModel]);

        $dispatcher->dispatch($route, $controller, 'update');
    }

    public function test_get_middleware_returns_controller_middleware(): void
    {
        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestControllerWithMiddleware;

        $middleware = $dispatcher->getMiddleware($controller, 'index');

        $this->assertCount(1, $middleware);
        $this->assertSame('auth', $middleware[0]);
    }

    public function test_get_middleware_excludes_methods(): void
    {
        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestControllerWithMiddleware;

        $middleware = $dispatcher->getMiddleware($controller, 'excludeMe');

        $this->assertCount(0, $middleware);
    }

    public function test_get_middleware_returns_empty_for_controller_without_middleware_method(): void
    {
        $dispatcher = new ControllerDispatcher(Container::getInstance());
        $controller = new TestControllerWithoutMiddleware;

        $middleware = $dispatcher->getMiddleware($controller, 'index');

        $this->assertCount(0, $middleware);
    }
}

class TestAuthorizeController extends Controller
{
    #[Authorize('viewAny')]
    public function index(): string
    {
        return 'index result';
    }
}

class TestAuthorizeControllerWithArgs extends Controller
{
    #[Authorize('create', ['TestClass'])]
    public function store(): string
    {
        return 'store result';
    }
}

class TestAuthorizeControllerWithDynamicArgs extends Controller
{
    #[Authorize('update', ['$post'])]
    public function update(): string
    {
        return 'update result';
    }
}

class TestAuthorizeControllerMultiple extends Controller
{
    #[Authorize('update')]
    #[Authorize('delete')]
    public function destroy(): string
    {
        return 'destroy result';
    }
}

class TestControllerWithoutAuthorize extends Controller
{
    public function index(): string
    {
        return 'index result';
    }
}

class TestControllerWithCallAction extends Controller
{
    #[Authorize('viewAny')]
    public function index(): string
    {
        return 'method result';
    }

    public function callAction($method, $parameters): string
    {
        return 'callAction result';
    }
}

class TestControllerWithCallMethod extends Controller
{
    public function __call($method, $parameters): string
    {
        throw new \BadMethodCallException("Method {$method} does not exist.");
    }
}

enum TestAbility
{
    case ViewAny;
}

class TestAuthorizeControllerWithEnum extends Controller
{
    #[Authorize(TestAbility::ViewAny)]
    public function index(): string
    {
        return 'index result';
    }
}

class TestAuthorizeControllerWithMixedArgs extends Controller
{
    #[Authorize('edit', ['$post', 'admin'])]
    public function update(): string
    {
        return 'update result';
    }
}

class TestModel extends \Illuminate\Database\Eloquent\Model
{
}

class TestControllerWithMiddleware extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['excludeMe']]);
    }

    public function index(): string
    {
        return 'index';
    }

    public function excludeMe(): string
    {
        return 'excludeMe';
    }
}

class TestControllerWithoutMiddleware extends Controller
{
    public function index(): string
    {
        return 'index';
    }
}
