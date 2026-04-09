<?php

namespace MVC\controller;

use MVC\core\CSRFToken;
use MVC\middleware\AuthMiddleware;
use MVC\model\User;

class AuthController extends Controller
{
    public function login(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            header('Location: home');
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Login'])) {
            if (!CSRFToken::validateRequest()) {
                $error = 'Invalid or expired session. Please refresh and try again.';
                return $this->render('home.login', ['error' => $error]);
            }

            $username = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                $error = 'Username and password are required.';
                return $this->render('home.login', ['error' => $error]);
            }

            $user = User::findByUsername($username);
            if (!$user) {
                // Avoid user enumeration; behave like wrong password.
                sleep(random_int(2, 4));
                $error = 'Invalid username or password.';
                return $this->render('home.login', ['error' => $error]);
            }

            $total_failed_login = 30;
            $lockout_time_minutes = 1;

            $account_locked = false;
            if (!empty($user['failed_login']) && (int)$user['failed_login'] >= $total_failed_login) {
                $last_login = !empty($user['last_login']) ? strtotime((string)$user['last_login']) : 0;
                $timeout = $last_login + ($lockout_time_minutes * 60);
                if (time() < $timeout) {
                    $account_locked = true;
                }
            }

            $hashed = hash('sha256', $password);
            if (!$account_locked && hash_equals((string)$user['User_Password'], $hashed)) {
                User::updateFailedLogin($username, 0);
                User::updateLastLogin($username);

                $_SESSION['Username'] = $username;
                $_SESSION['role'] = $user['AdminID'];

                if (class_exists('MVC\core\session\Session')) {
                    \MVC\core\session\Session::updateLoginCount();
                }

                error_log("Successful login for user: $username from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

                header('Location: home');
                exit;
            }

            // Login failed
            sleep(random_int(2, 4));
            User::incrementFailedLogin($username);
            User::updateLastLogin($username);
            error_log("Failed login attempt for user: $username from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $error = $account_locked ? 'Account locked. Please try again later.' : 'Invalid username or password.';
        }

        return $this->render('home.login', ['error' => $error]);
    }

    public function register(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            header('Location: home');
            exit;
        }

        $error_message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            if (!CSRFToken::validateRequest()) {
                $error_message = 'Invalid or expired session. Please refresh and try again.';
                return $this->render('home.register', ['error_message' => $error_message]);
            }

            $name = trim((string)($_POST['name'] ?? ''));
            $username = trim((string)($_POST['username'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $pass = (string)($_POST['pass'] ?? '');
            $c_pass = (string)($_POST['c_pass'] ?? '');
            $phonenumber = trim((string)($_POST['phonenumber'] ?? ''));
            $national_id = trim((string)($_POST['ID'] ?? ''));

            if ($name === '' || $username === '' || $email === '' || $pass === '' || $c_pass === '' || $phonenumber === '' || $national_id === '') {
                $error_message = 'All fields are required!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = 'Invalid email format!';
            } elseif (strlen($username) < 8) {
                $error_message = 'Username must be at least 8 characters long!';
            } elseif (strlen($pass) < 8) {
                $error_message = 'Password must be at least 8 characters long!';
            } elseif ($pass !== $c_pass) {
                $error_message = 'Passwords do not match!';
            } elseif (User::existsByUsername($username)) {
                $error_message = 'Username already exists!';
            } elseif (User::existsByEmail($email)) {
                $error_message = 'Email already exists!';
            } elseif (User::existsByNationalID($national_id)) {
                $error_message = 'National ID already registered!';
            } else {
                $next_user_number = User::nextUserNumber();
                $hashed_password = hash('sha256', $pass);

                $con = \Database::getConnection();
                $stmt = $con->prepare('INSERT INTO user (
                    User_Number,
                    User_Name,
                    User_FullName,
                    User_Password,
                    User_Email,
                    User_Avatar,
                    failed_login,
                    date,
                    AdminID,
                    SourceID,
                    User_Phone,
                    User_NationalID
                ) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)');

                $ok = $stmt->execute([
                    $next_user_number,
                    $username,
                    $name,
                    $hashed_password,
                    $email,
                    'images/pic-6.jpg',
                    0,
                    AuthMiddleware::ROLE_USER,
                    0,
                    $phonenumber,
                    $national_id
                ]);

                if ($ok) {
                    header("Location: login");
                    exit;
                }

                $error_message = 'Registration failed. Please try again.';
            }
        }

        return $this->render('home.register', ['error_message' => $error_message]);
    }
}

