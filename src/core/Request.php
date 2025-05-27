<?php

namespace MVC\core;

use MVC\core\session\Session;

class Request
{
    public static array $globalWeb = [
        Session::class,
        //CSRFToken::class
    ];
    public static array $globalApi = [];
    public function __construct()
    {
        $this->routeBasedOnPath();
    }
    public static function path(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $path = filter_var($path, FILTER_SANITIZE_URL);
        $position = strpos($path, '?');
        $path = $position !== false ? substr($path, 0, $position) : $path;
        return $path === '/' ? '/' : ltrim($path, '/');
    }
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }
    public function routeBasedOnPath(): void
    {
        self::isApiRequest() ? self::apiRoute() : self::webRoute();
    }
    public static function isApiRequest(): bool
    {
        return str_starts_with(self::path(), 'api');
    }
    public static function apiRoute(): void
    {
        foreach (static::$globalApi as $global) {
            new $global();
        }
        include route_path('api.php');
    }
    public static function webRoute(): void
    {
        foreach (static::$globalWeb as $global) {
            new $global();
        }
        //CSRFToken::getToken();
        include route_path('web.php');
    }
    /**
    public static function segments(int $offset): string
    {
        $segments = explode('/', self::path());
        return $segments[$offset] ?? '';
    }
     * */
}