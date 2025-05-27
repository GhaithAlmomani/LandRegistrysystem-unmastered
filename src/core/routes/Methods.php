<?php

namespace MVC\core\routes;

trait Methods
{
    public static function get(string $route, callable|array $handler, array $middleware = []): void {
        static::add('GET', $route, $handler, $middleware);
    }
    public static function post(string $route, callable|array $handler, array $middleware = []): void {
        static::add('POST', $route, $handler, $middleware);
    }
}