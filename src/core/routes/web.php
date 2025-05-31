<?php

use MVC\controller\HomeController;
use MVC\controller\SearchController;
use MVC\controller\LogoutController;
use MVC\core\Router;

Router::group([], function (){
    Router::get('/', [HomeController::class, 'index']);
    Router::get('about', [HomeController::class, 'about']);
});

Router::get('team', [HomeController::class, 'team']);

Router::get('home', [HomeController::class, 'home']);

Router::get('login', [HomeController::class, 'login']);
Router::post('login', [HomeController::class, 'login']);

Router::get('contact', [HomeController::class, 'contact']);

Router::get('connect', [HomeController::class, 'connect']);
Router::post('connect', [HomeController::class, 'connect']);

Router::get('employee', [HomeController::class, 'employee']);

Router::get('ownedassets', [HomeController::class, 'ownedassets']);

Router::get('profile', [HomeController::class, 'profile']);

Router::get('register', [HomeController::class, 'register']);
Router::post('register', [HomeController::class, 'register']);

Router::get('interact', [HomeController::class, 'interact']);

Router::get('learn-more', [HomeController::class, 'learnMore']);

Router::get('adminAddress', [HomeController::class, 'adminAddress']);

Router::get('update-profile', [HomeController::class, 'updateProfile']);
Router::post('update-profile', [HomeController::class, 'updateProfile']);

Router::get('watch-video', [HomeController::class, 'watchVideo']);

Router::get('watch-video2', [HomeController::class, 'watchVideo2']);

Router::get('propertyRegistration', [HomeController::class, 'propertyRegistration']);

Router::get('propertyTransfer', [HomeController::class, 'propertyTransfer']);

Router::get('setEmpAuth', [HomeController::class, 'setEmpAuth']);

Router::get('qrScan', [HomeController::class, 'qrScan']);

// Search Routes
Router::get('search', [SearchController::class, 'index']);

Router::get('testEmp', [HomeController::class, 'testEmp']);

Router::get('checkEmpAuth', [HomeController::class, 'checkEmpAuth']);

Router::get('allProperties', [HomeController::class, 'allProperties']);

Router::get('PropertyById', [HomeController::class, 'PropertyById']);

Router::get('PropertyCount', [HomeController::class, 'PropertyCount']);

Router::get('PropertyInfo', [HomeController::class, 'PropertyInfo']);

Router::get('sell', [HomeController::class, 'sell']);

Router::get('sellReq', [HomeController::class, 'sellReq']);
Router::post('sellReq', [HomeController::class, 'sellReq']);

Router::get('sellRequest', [HomeController::class, 'sellRequest']);
Router::post('sellRequest', [HomeController::class, 'sellRequest']);

Router::get('recentTransaction', [HomeController::class, 'recentTransaction']);

Router::get('employeePortal', [HomeController::class, 'employeePortal']);

Router::get('adminPortal', [HomeController::class, 'adminPortal']);

Router::get('orders', [HomeController::class, 'orders']);

// Logout Route
Router::get('logout', [LogoutController::class, 'index']);

Router::get('dashboard', [HomeController::class, 'dashboard']);

Router::post('dashboard', [HomeController::class, 'dashboard']);

