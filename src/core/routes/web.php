<?php

use MVC\controller\HomeController;
use MVC\controller\AuthController;
use MVC\controller\AdminController;
use MVC\controller\EmployeeController;
use MVC\controller\PropertyController;
use MVC\controller\TransferController;
use MVC\controller\UserController;
use MVC\controller\SearchController;
use MVC\controller\LogoutController;
use MVC\core\Router;

Router::group([], function (){
    Router::get('/', [HomeController::class, 'index']);
    Router::get('about', [HomeController::class, 'about']);
});

Router::get('team', [HomeController::class, 'team']);

Router::get('home', [UserController::class, 'home']);

Router::get('login', [AuthController::class, 'login']);
Router::post('login', [AuthController::class, 'login']);

Router::get('contact', [HomeController::class, 'contact']);
Router::post('contact', [HomeController::class, 'contact']);

Router::get('connect', [HomeController::class, 'connect']);
Router::post('connect', [HomeController::class, 'connect']);

Router::get('employee', [EmployeeController::class, 'employee']);

Router::get('ownedassets', [PropertyController::class, 'ownedassets']);

Router::get('profile', [UserController::class, 'profile']);

Router::get('register', [AuthController::class, 'register']);
Router::post('register', [AuthController::class, 'register']);

Router::get('interact', [HomeController::class, 'interact']);

Router::get('learn-more', [HomeController::class, 'learnMore']);

Router::get('adminAddress', [AdminController::class, 'adminAddress']);
Router::post('adminAddress', [AdminController::class, 'adminAddress']);

Router::get('update-profile', [UserController::class, 'updateProfile']);
Router::post('update-profile', [UserController::class, 'updateProfile']);

Router::get('watch-video', [HomeController::class, 'watchVideo']);

Router::get('watch-video2', [HomeController::class, 'watchVideo2']);

Router::get('propertyRegistration', [EmployeeController::class, 'propertyRegistration']);
Router::post('propertyRegistration', [EmployeeController::class, 'propertyRegistration']);

Router::get('propertyTransfer', [EmployeeController::class, 'propertyTransfer']);
Router::post('propertyTransfer', [EmployeeController::class, 'propertyTransfer']);

Router::get('setEmpAuth', [AdminController::class, 'setEmpAuth']);
Router::post('setEmpAuth', [AdminController::class, 'setEmpAuth']);

Router::get('qrScan', [UserController::class, 'qrScan']);

// Search Routes (GET: URL params; POST: header search form uses search_box)
Router::get('search', [SearchController::class, 'index']);
Router::post('search', [SearchController::class, 'index']);

Router::get('testEmp', [EmployeeController::class, 'testEmp']);

Router::get('checkEmpAuth', [AdminController::class, 'checkEmpAuth']);

Router::get('allProperties', [PropertyController::class, 'allProperties']);

Router::get('PropertyById', [PropertyController::class, 'PropertyById']);

Router::get('PropertyCount', [PropertyController::class, 'PropertyCount']);

Router::get('PropertyInfo', [PropertyController::class, 'PropertyInfo']);

Router::get('sell', [PropertyController::class, 'sell']);

Router::get('sellReq', [TransferController::class, 'sellReq']);
Router::post('sellReq', [TransferController::class, 'sellReq']);

Router::get('sellRequest', [TransferController::class, 'sellRequest']);
Router::post('sellRequest', [TransferController::class, 'sellRequest']);
Router::get('downloadDocument', [TransferController::class, 'downloadDocument']);
Router::post('downloadDocument', [TransferController::class, 'downloadDocument']);

Router::get('recentTransaction', [UserController::class, 'recentTransaction']);

Router::get('employeePortal', [EmployeeController::class, 'employeePortal']);

Router::get('adminPortal', [AdminController::class, 'adminPortal']);

Router::get('orders', [UserController::class, 'orders']);

// Logout Route
Router::get('logout', [LogoutController::class, 'index']);

Router::get('dashboard', [UserController::class, 'dashboard']);
Router::post('dashboard', [UserController::class, 'dashboard']);

