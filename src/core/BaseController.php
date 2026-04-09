<?php

namespace MVC\core;

use MVC\core\validations\Validation;

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

        // Provide current user to layouts (no DB queries in views).
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!array_key_exists('currentUser', $this->layoutParams)) {
            $this->layoutParams['currentUser'] = null;
            if (!empty($_SESSION['Username']) && class_exists(\MVC\model\User::class)) {
                try {
                    $this->layoutParams['currentUser'] = \MVC\model\User::findByUsername((string)$_SESSION['Username']);
                } catch (\Throwable $e) {
                    $this->layoutParams['currentUser'] = null;
                }
            }
        }

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