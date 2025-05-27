<?php

namespace MVC\core;

class AutoLoader
{
    public static function register(): void
    {
        spl_autoload_register(array(__CLASS__, 'load'));
    }
    public static function load(string $className): void
    {
        $className = str_replace('MVC\\', '' , $className);
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $filePath  = APP_PATH . $className . '.php';
        if (file_exists($filePath)){
            require_once $filePath;
        } else {
            throw new Log("Class file not found: $filePath");
        }
    }
}
AutoLoader::register();