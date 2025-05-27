<?php

namespace MVC\core;

use MVC\core\routes\Methods;
use MVC\core\middleware\CSRFMiddleware;

class Router
{
    use Methods;
    protected Request $request;
    protected Response $response;
    protected static array $routes = [];
    protected static array $groupAttributes = [];
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
    private function compilePattern($uri): string
    {
        $pattern = preg_replace('/{([a-zA-Z0-9_]+)}/', '(?P<$1>[a-zA-Z0-9_]+)', $uri);
        return "#^$pattern$#";
    }
    private function invokeHandler($handler, $params)
    {
        if (is_callable($handler)){
            return call_user_func($handler, ...$params);
        }
        if (is_array($handler)) {
            [$controller, $action] = $handler;
            return call_user_func_array([new $controller, $action], $params);
        }
        throw new Log("Handler is not callable or a valid [controller, action] array.",500);
    }
    public static function add(string $method, string $route, callable|array $handler, array $middleware = []): void {
        $route      = self::applyGroupPrefix($route);
        $middleware = array_merge(static::getGroupMiddleware(), $middleware);
        self::$routes[] = [
            'method'    => $method,
            'uri'       => $route === '/' ? $route : ltrim($route, '/'),
            'handler'   => $handler,
            'middleware'=> $middleware
        ];
    }
    protected static function applyGroupPrefix($route): string
    {
        if (isset(static::$groupAttributes['prefix'])) {
            $full_route = rtrim(static::$groupAttributes['prefix'], '/') . '/' . ltrim($route, '/');
            return rtrim(ltrim($full_route, '/'), '/');
        } else {
            return $route;
        }
    }
    public static function group($attributes, $callback): void
    {
        $previousGroupAttribute  = self::$groupAttributes;
        self::$groupAttributes = array_merge(self::$groupAttributes, $attributes);
        call_user_func($callback);
        self::$groupAttributes = $previousGroupAttribute;
    }
    protected static function getGroupMiddleware(): array
    {
        return static::$groupAttributes['middleware'] ?? [];
    }
    private function handleMiddleware(array $middleware, callable $next)
    {
        // Add CSRF middleware for non-GET requests
        if ($this->request->method() !== 'GET') {
            $csrfMiddleware = new CSRFMiddleware();
            if (!$csrfMiddleware->handle()) {
                return false;
            }
        }

        // Handle other middleware
        foreach ($middleware as $m) {
            if (is_string($m)) {
                $m = new $m();
            }
            if (!$m->handle()) {
                return false;
            }
        }

        return $next();
    }
    public function dispatch()
    {
        $path   = $this->request->path();
        $method = $this->request->method();
        foreach (self::$routes as $route) {
            $pattern = $this->compilePattern($route['uri']);
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)){
                $params     = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler    = $route['handler'];
                $middleware = $route['middleware'];
                $next = function () use ($handler, $params) {
                    return $this->invokeHandler($handler, $params);
                };
                return $this->handleMiddleware($middleware, $next);
            }
        }
        throw new Log("Page Not Found",404);
    }
}