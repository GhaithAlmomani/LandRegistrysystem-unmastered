<?php

namespace MVC\core\middleware;

use MVC\core\CSRFToken;
use MVC\core\Response;

class CSRFMiddleware
{
    public function handle(): bool
    {
        // Only validate POST, PUT, DELETE requests
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        if (!CSRFToken::validateRequest()) {
            // If it's an AJAX request, return JSON response
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                Response::json(['error' => 'CSRF token validation failed'], 403);
            } else {
                // For regular requests, redirect to error page
                Response::redirect('error/csrf');
            }
            return false;
        }

        return true;
    }
} 