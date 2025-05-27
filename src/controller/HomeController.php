<?php

namespace MVC\controller;

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

    public function home(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if($_SESSION['role'] == 3){
                return $this->render('home.Admin.adminPortal');
            }
            else{
                if($_SESSION['role'] == 2) {
                    return $this->render('home.Employee.employeePortal');
                }
                else{
                    return $this->render('home.User.home');
                }
            }
        } else {
            header('Location: login');
        }
    }

    public function login(): bool|array|string
    {
        return $this->render('home.login');
    }

    public function contact(): bool|array|string
    {
        return $this->render('home.contact');
    }
    public function connect(): bool|array|string
    {
        return $this->render('home.connect');
    }
    public function employee(): bool|array|string
    {
        return $this->render('home.employee');
    }
    public function ownedassets(): bool|array|string
    {
        return $this->render('home.ownedassets');
    }
    public function profile(): bool|array|string
    {
        return $this->render('home.profile');
    }
    public function register(): bool|array|string
    {
        return $this->render('home.register');
    }
    public function interact(): bool|array|string
    {
        return $this->render('home.interact');
    }

    public function learnMore(): bool|array|string
    {
        return $this->render('home.learn-more');
    }

    public function adminAddress(): bool|array|string
    {
        return $this->render('home.Admin.adminAddress');
    }

    public function updateProfile(): bool|array|string
    {
        return $this->render('home.update-profile');
    }

    public function watchVideo(): bool|array|string
    {
        return $this->render('home.watch-video');
    }
    public function watchVideo2(): bool|array|string
    {
        return $this->render('home.watch-video2');
    }

    public function propertyRegistration(): bool|array|string
    {
        return $this->render('home.propertyRegistration');
    }

    public function propertyTransfer(): bool|array|string
    {
        return $this->render('home.propertyTransfer');
    }
    public function setEmpAuth(): bool|array|string
    {
        return $this->render('home.setEmpAuth');
    }
    public function qrScan(): bool|array|string
    {
        return $this->render('home.qrScan');
    }

    public function testEmp(): bool|array|string
    {
        return $this->render('home.testEmp');
    }

    public function checkEmpAuth(): bool|array|string
    {
        return $this->render('home.checkEmpAuth');
    }

    public function allProperties(): bool|array|string
    {
        return $this->render('home.allProperties');
    }

    public function PropertyById(): bool|array|string
    {
        return $this->render('home.PropertyById');
    }

    public function PropertyCount(): bool|array|string
    {
        return $this->render('home.PropertyCount');
    }

    public function PropertyInfo(): bool|array|string
    {
        return $this->render('home.PropertyInfo');
    }

    public function sell(): bool|array|string
    {
        return $this->render('home.sell');
    }

    public function dashboard(): bool|array|string
    {
        return $this->render('home.dashboard');
    }

    public function sellReq(): bool|array|string
    {
        return $this->render('home.sellReq');
    }

    public function recentTransaction(): bool|array|string
    {
        return $this->render('home.recentTransaction');
    }

    public function employeePortal(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if($_SESSION['role'] == 2){
                return $this->render('home.Employee.employeePortal');
            } else {
                header('Location: home');
            }
        } else {
            header('Location: login');
        }
    }

    public function adminPortal(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if($_SESSION['role'] == 3){
                return $this->render('home.Admin.adminPortal');
            } else {
                header('Location: home');
            }
        } else {
            header('Location: login');
        }
    }
}
