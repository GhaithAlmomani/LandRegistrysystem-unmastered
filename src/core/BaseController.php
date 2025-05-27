<?php

namespace MVC\core;

use MVC\core\validations\Validation;
use PDO;
use PDOException;

abstract class BaseController
{
    protected array $layoutParams = [];
    public function setLayoutParam(string $key, $value): void
    {
        $this->layoutParams[$key] = $value;
    }
    public function render(string $view, array $params = []): bool|array|string
    {
        $this->connectDatabase();
        try {
            return View::renderView($view, array_merge($this->layoutParams, $params));
        } catch (Log $e) {
            return View::renderView('error.index', [
                'exception' => $e
            ]);
        }
    }
    public function validate(array|object $requests, array $rules, array|null $attributes = []): Validation
    {
        return Validation::make($requests, $rules, $attributes);
    }
    public function connectDatabase(): void
    {

    }
}