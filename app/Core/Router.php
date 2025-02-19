<?php
namespace App\Core;

use App\Utilities\Error;
use Closure;
use Exception;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private array $groupStack = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }

    public function add(string $method, string $route, $handler, array $middleware = []): void
    {
        $method = strtoupper($method);
        if ($prefix = $this->getGroupAttribute('prefix')) {
            $route = rtrim($prefix, '/') . '/' . ltrim($route, '/');
        }

        $middleware = array_merge($this->getGroupAttribute('middleware', []), $middleware);

        $this->routes[$method][$route] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    private function getGroupAttribute(string $attribute, $default = null)
    {
        return array_reduce($this->groupStack, function ($carry, $group) use ($attribute) {
            return $group[$attribute] ?? $carry;
        }, $default) ?? $default;
    }

    /**
     * @throws Exception
     */
    public function dispatch(Request $request, Response $response)
    {
        $requestUri = rtrim(parse_url($request->getUri(), PHP_URL_PATH), '/') ?: '/';
        $requestMethod = $request->getMethod();

        if (!isset($this->routes[$requestMethod])) {
            throw new Exception("HTTP method $requestMethod is not supported.", 405);
        }

        foreach ($this->routes[$requestMethod] as $route => $routeInfo) {
            $pattern = $this->convertRouteToPattern($route);

            if (preg_match($pattern, $requestUri, $matches)) {
                $this->runMiddleware($routeInfo['middleware'] ?? [], $matches);

                return $this->handleRoute($routeInfo['handler'], $matches);
            }
        }

        Error::throw(404);
    }

    private function convertRouteToPattern(string $route): string
    {
        $pattern = preg_replace('#\{(\w+)}#', '(?P<$1>[a-zA-Z0-9\_\-\.]+)', $route);
        return "#^" . rtrim($pattern, '/') . "/?$#";
    }

    /**
     * @throws Exception
     */
    private function runMiddleware(array $middleware, array $matches): void
    {
        foreach ($middleware as $middlewareClass) {
            $mwInstance = $this->container->make($middlewareClass);
            $mwInstance->handle($matches);
        }
    }

    /**
     * @throws Exception
     */
    private function handleRoute($handler, array $matches)
    {
        if ($handler instanceof Closure) {
            return $handler($matches);
        } elseif (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler);
            $controller = 'App\\Controllers\\' . $controller;
            $controller = $this->container->make($controller);
            if (!method_exists($controller, $method)) {
                throw new Exception("Method $method not found in controller " . get_class($controller));
            }
            return call_user_func_array([$controller, $method], [$matches]);
        } else {
            throw new Exception("Invalid route handler format");
        }
    }
}
