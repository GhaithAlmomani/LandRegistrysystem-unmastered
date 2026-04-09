<?php

namespace MVC\controller;

use MVC\middleware\AuthMiddleware;

class AdminController extends Controller
{
    public function adminPortal(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.adminPortal');
    }

    public function setEmpAuth(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.setEmpAuth');
    }

    public function checkEmpAuth(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.checkEmpAuth');
    }

    public function adminAddress(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.adminAddress');
    }
}

