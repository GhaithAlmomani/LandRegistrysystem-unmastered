<?php

namespace MVC\controller;

use MVC\core\BaseController;
use MVC\core\session\Session;

class LogoutController extends BaseController
{
    public function index()
    {
        // Clear all session data
        Session::forget_all();
        
        // Regenerate session ID for security
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        
        // Redirect to home page
        header('Location: ' . url('home'));
        exit;
    }
} 