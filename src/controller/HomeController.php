<?php

namespace MVC\controller;

require_once __DIR__ . '/../../config/database.php';

use MVC\middleware\AuthMiddleware;

class HomeController extends Controller
{
    public function index(): bool|array|string
    {
        return $this->render('home.index');
    }
    public function about(): bool|array|string
    {
        return $this->render('home.about');
    }
    public function team(): bool|array|string
    {
        return $this->render('home.team');
    }

    public function contact(): bool|array|string
    {
        return $this->render('home.contact');
    }
    public function connect(): bool|array|string
    {
        return $this->render('home.connect');
    }
    public function interact(): bool|array|string
    {
        return $this->render('home.interact');
    }

    public function learnMore(): bool|array|string
    {
        return $this->render('home.learn-more');
    }

    public function watchVideo(): bool|array|string
    {
        return $this->render('home.watch-video');
    }
    public function watchVideo2(): bool|array|string
    {
        return $this->render('home.watch-video2');
    }
}
