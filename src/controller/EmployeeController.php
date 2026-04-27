<?php

namespace MVC\controller;

require_once __DIR__ . '/../../config/database.php';

use MVC\middleware\AuthMiddleware;
use MVC\core\CSRFToken;

class EmployeeController extends Controller
{
    private function respondJson(int $statusCode, array $payload): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function postString(string $key, int $maxLen, bool $required = true): array
    {
        $raw = $_POST[$key] ?? null;
        if ($raw === null) {
            return [$required ? null : '', $required ? "$key is required" : null];
        }
        $value = trim((string)$raw);
        if ($required && $value === '') {
            return [null, "$key is required"];
        }
        $len = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
        if ($value !== '' && $len > $maxLen) {
            return [null, "$key must be at most {$maxLen} characters"];
        }
        return [$value, null];
    }

    private function postFloat(string $key, bool $required = true, ?float $min = null, ?float $max = null): array
    {
        $raw = $_POST[$key] ?? null;
        if ($raw === null) {
            return [$required ? null : null, $required ? "$key is required" : null];
        }
        $value = trim((string)$raw);
        if ($required && $value === '') {
            return [null, "$key is required"];
        }
        if ($value === '') {
            return [null, null];
        }
        if (!is_numeric($value)) {
            return [null, "$key must be a number"];
        }
        $floatVal = (float)$value;
        if ($min !== null && $floatVal < $min) {
            return [null, "$key must be >= {$min}"];
        }
        if ($max !== null && $floatVal > $max) {
            return [null, "$key must be <= {$max}"];
        }
        return [$floatVal, null];
    }

    private function postWalletAddress(string $key, bool $required = true): array
    {
        [$value, $err] = $this->postString($key, 42, $required);
        if ($err !== null) {
            return [null, $err];
        }
        if ($value === '') {
            return ['', null];
        }
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
            return [null, "$key must be a valid wallet address (0x + 40 hex chars)"];
        }
        return [$value, null];
    }

    private function requireValidCsrfOrFailJson(): void
    {
        if (!CSRFToken::validateRequest()) {
            $this->respondJson(419, ['ok' => false, 'errors' => ['csrf_token' => ['Invalid or expired CSRF token']]]);
        }
    }

    public function employee(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.employee');
    }

    public function employeePortal(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.employeePortal');
    }

    public function testEmp(): bool|array|string
    {
        return $this->render('home.testEmp');
    }

    public function propertyRegistration(): bool|array|string
    {
        AuthMiddleware::requireEmployee();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireValidCsrfOrFailJson();

            $errors = [];

            [$owner, $e] = $this->postWalletAddress('owner', true);
            if ($e) $errors['owner'][] = $e;

            [$description, $e] = $this->postString('description', 255, true);
            if ($e) $errors['description'][] = $e;

            [$latitude, $e] = $this->postFloat('latitude', true, -90, 90);
            if ($e) $errors['latitude'][] = $e;

            [$longitude, $e] = $this->postFloat('longitude', true, -180, 180);
            if ($e) $errors['longitude'][] = $e;

            if (!empty($errors)) {
                $this->respondJson(422, ['ok' => false, 'errors' => $errors]);
            }

            $this->respondJson(200, ['ok' => true]);
        }

        return $this->render('home.Employee.propertyRegistration');
    }

    public function propertyTransfer(): bool|array|string
    {
        AuthMiddleware::requireEmployee();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireValidCsrfOrFailJson();

            $errors = [];

            [$propertyId, $e] = $this->postString('propertyId', 64, true);
            if ($e) $errors['propertyId'][] = $e;

            [$previousOwner, $e] = $this->postWalletAddress('previousOwner', true);
            if ($e) $errors['previousOwner'][] = $e;

            [$newOwner, $e] = $this->postWalletAddress('newOwner', true);
            if ($e) $errors['newOwner'][] = $e;

            if (!empty($errors)) {
                $this->respondJson(422, ['ok' => false, 'errors' => $errors]);
            }

            $this->respondJson(200, ['ok' => true]);
        }

        return $this->render('home.Employee.propertyTransfer');
    }
}

