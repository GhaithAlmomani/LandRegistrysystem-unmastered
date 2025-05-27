<?php
// Example config function
if (!function_exists('config')) {
    function config(?string $file = null)
    {
        // Split the input string by dots
        $separate = explode('.', $file);
        // Check if the split array is valid and the input is not null
        if ((!empty($separate) && count($separate) > 1) && !is_null($file)) {
            // Build the path to the configuration file and include it
            $file = include APP_PATH . 'core' . DS . 'config' . DS . $separate[0] . '.php';
            // Return the specific configuration value or the whole array if the key doesn't exist
            return $file[$separate[1]] ?? $file;
        }
        // If the input didn't match the conditions, return it as is
        return $file;
    }
}
// Define the route_path function
if (!function_exists('route_path')) {
    function route_path(?string $file = null)
    {
        return !is_null($file) ? config('MVC.route') . DS . $file : config('MVC.route');
    }
}
// URL function
if (!function_exists('url')) {
    function url(string $url = ''): string
    {
        $protocol   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
        $port       = $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':' . $_SERVER['SERVER_PORT'];
        return $protocol . $_SERVER['SERVER_NAME'] . $port . '/' . ltrim($url, '/');
    }
}
//Check if the given page is the current active page.
if (!function_exists('isActive')) {
    function isActive(string $page): string
    {
        // Extract the path component from the current request URI
        $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Normalize the page input to ensure consistent comparison
        $page = trim($page, '/');

        // Return 'active' if the current URI matches the given page, otherwise return an empty string
        return $currentUri === $page ? 'active' : '';
    }
}
//Generate an SVG element with the specified symbol, width, and height.
if (!function_exists('svg')) {
    function svg(string $symbol, string $width = "16", string $height = "16"): string
    {
        return '
            <svg width="' . $width . '" height="' . $height . '" class="bi mx-1 theme-icon-active">
                <use href="' . url('style/image/') . 'icons.svg#' . $symbol . '"></use>
            </svg>
        ';
    }
}