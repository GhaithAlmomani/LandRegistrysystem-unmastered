<?php

namespace MVC\core;

class View
{
    private const string CONFIG_VIEW_PATH = 'MVC.view';
    public static function renderView(string $view, ?array $params): bool|string
    {
        $view = str_replace('.', DS, $view);
        $path = config(self::CONFIG_VIEW_PATH);
        $viewPath = $path . $view . '.tpl.php';
        if (!file_exists($viewPath)) {
            http_response_code(404);
            throw new Log("View file not found: $view",404);
        }
        if ($params) {
            extract($params, EXTR_SKIP);
        }

        ob_start();
        include $viewPath;
        $viewContent = ob_get_clean();

        $layoutContent = self::layoutContent($params ?? []);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
    protected static function layoutContent(array $params): false|string
    {
        extract($params, EXTR_SKIP);
        ob_start();
        include_once config(self::CONFIG_VIEW_PATH) . 'layouts' . DS . 'main.tpl.php';
        return ob_get_clean();
    }
}