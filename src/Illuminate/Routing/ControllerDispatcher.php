<?php

namespace Illuminate\Routing;

use Illuminate\Auth\Attributes\Authorize;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Support\Collection;

class ControllerDispatcher implements ControllerDispatcherContract
{
    use FiltersControllerMiddleware, ResolvesRouteDependencies;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Create a new controller dispatcher instance.
     *
     * @param  \Illuminate\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @return mixed
     */
    public function dispatch(Route $route, $controller, $method)
    {
        $this->authorizeAttribute($route, $controller, $method, $route->parameters());

        $parameters = $this->resolveParameters($route, $controller, $method);

        if (method_exists($controller, 'callAction')) {
            return $controller->callAction($method, $parameters);
        }

        return $controller->{$method}(...array_values($parameters));
    }

    /**
     * Resolve the parameters for the controller.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @return array
     */
    protected function resolveParameters(Route $route, $controller, $method)
    {
        return $this->resolveClassMethodDependencies(
            $route->parametersWithoutNulls(), $controller, $method
        );
    }

    /**
     * Get the middleware for the controller instance.
     *
     * @param  \Illuminate\Routing\Controller  $controller
     * @param  string  $method
     * @return array
     */
    public function getMiddleware($controller, $method)
    {
        if (! method_exists($controller, 'getMiddleware')) {
            return [];
        }

        return (new Collection($controller->getMiddleware()))
            ->reject(fn ($data) => static::methodExcludedByOptions($method, $data['options']))
            ->pluck('middleware')
            ->all();
    }

    /**
     * Authorize the #[Authorize] attribute on the controller method.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     */
    protected function authorizeAttribute(Route $route, $controller, $method, array $parameters): void
    {
        $classReflection = new \ReflectionClass($controller);

        if (! $classReflection->hasMethod($method)) {
            return;
        }

        $methodReflection = $classReflection->getMethod($method);
        $methodAttributes = $methodReflection->getAttributes(Authorize::class);

        foreach ($methodAttributes as $attribute) {
            $arguments = [];

            foreach ((array) ($attribute->getArguments()[1] ?? []) as $attributeArguments) {
                if (str_starts_with($attributeArguments, '$')) {
                    $parameterName = substr($attributeArguments, 1);

                    if (! array_key_exists($parameterName, $parameters)) {
                        throw new \InvalidArgumentException("Missing parameter [{$parameterName}] for authorization.");
                    }

                    $arguments[] = $parameters[$parameterName];
                } else {
                    $arguments[] = $attributeArguments;
                }
            }

            $this->container->make(Gate::class)->authorize(
                $attribute->getArguments()[0],
                $arguments,
            );
        }
    }
}
