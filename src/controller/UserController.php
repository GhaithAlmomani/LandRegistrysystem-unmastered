<?php

namespace MVC\controller;

use MVC\core\CSRFToken;
use MVC\middleware\AuthMiddleware;
use MVC\model\User;
use MVC\model\Property;
use MVC\model\PropertyTransfer;
use Exception;

class UserController extends Controller
{
    public function home(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if ($_SESSION['role'] == AuthMiddleware::ROLE_ADMIN) {
                return $this->render('home.Admin.adminPortal');
            }
            if ($_SESSION['role'] == AuthMiddleware::ROLE_EMPLOYEE) {
                return $this->render('home.Employee.employeePortal');
            }
            return $this->render('home.User.home');
        }
        header('Location: /login');
        exit;
    }

    public function profile(): bool|array|string
    {
        if (!isset($_SESSION['Username'])) {
            header('Location: login');
            exit;
        }

        $userData = User::findByUsername($_SESSION['Username']);
        $assetsOwned = 0;
        $ordersCount = 0;

        if ($userData) {
            try {
                $owned = Property::findByOwner((int)$userData['User_ID']);
                $assetsOwned = is_array($owned) ? count($owned) : 0;
            } catch (Exception) {
                $assetsOwned = 0;
            }

            try {
                $ordersCount = PropertyTransfer::countForUser((int)$userData['User_ID'], (string)($userData['User_NationalID'] ?? ''));
            } catch (Exception) {
                $ordersCount = 0;
            }
        }

        return $this->render('home.profile', [
            'userData' => $userData,
            'assetsOwned' => $assetsOwned,
            'ordersCount' => $ordersCount,
        ]);
    }

    public function updateProfile(): bool|array|string
    {
        if (!isset($_SESSION['Username'])) {
            header('Location: login');
            exit;
        }

        $error_message = '';
        $success_message = '';

        $userData = User::findByUsername($_SESSION['Username']);
        if (!$userData) {
            $error_message = 'User not found.';
            return $this->render('home.update-profile', compact('error_message', 'success_message', 'userData'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRFToken::validateRequest()) {
                $error_message = 'Invalid or expired session. Please refresh and try again.';
                return $this->render('home.update-profile', compact('error_message', 'success_message', 'userData'));
            }

            $new_name = trim((string)($_POST['name'] ?? ''));
            $new_email = trim((string)($_POST['email'] ?? ''));
            $old_pass = (string)($_POST['old_pass'] ?? '');
            $new_pass = (string)($_POST['new_pass'] ?? '');
            $c_pass = (string)($_POST['c_pass'] ?? '');

            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid email format.";
            } else {
                $update = [
                    'User_Name' => $new_name,
                    'User_Email' => $new_email,
                ];

                if ($old_pass !== '' || $new_pass !== '' || $c_pass !== '') {
                    if (!password_verify($old_pass, (string)$userData['User_Password'])) {
                        $error_message = "Old password is incorrect.";
                    } elseif ($new_pass !== $c_pass) {
                        $error_message = "New passwords do not match.";
                    } elseif (strlen($new_pass) < 8) {
                        $error_message = "New password must be at least 8 characters.";
                    } else {
                        $update['User_Password'] = password_hash($new_pass, PASSWORD_ARGON2ID);
                    }
                }

                if ($error_message === '') {
                    User::update((int)$userData['User_ID'], $update);
                    $_SESSION['Username'] = $new_name;
                    $success_message = isset($update['User_Password']) ? "Profile and password updated successfully." : "Profile updated successfully.";
                    $userData = User::findByUsername($_SESSION['Username']);
                }
            }
        }

        return $this->render('home.update-profile', compact('error_message', 'success_message', 'userData'));
    }

    public function dashboard(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if ($_SESSION['role'] == AuthMiddleware::ROLE_EMPLOYEE) {
                return $this->render('home.Employee.dashboard');
            }
            if ($_SESSION['role'] == AuthMiddleware::ROLE_ADMIN) {
                // Controller provides the dashboard stats; view should not query DB.
                $total_users = \MVC\model\User::countAll();
                $total_assets = \MVC\model\Property::countAll();
                $total_orders = \MVC\model\PropertyTransfer::countAll();

                $etherscan_api_key = \Database::getEnv('ETHERSCAN_API_KEY');
                $contract_address = \Database::getEnv('ETHERSCAN_CONTRACT_ADDRESS');

                $days = [];
                $tx_per_day = [];
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $days[] = $date;
                    $tx_per_day[$date] = 0;
                }

                $tx_data = null;
                if ($etherscan_api_key && $contract_address) {
                    $tx_url = "https://api-sepolia.etherscan.io/api?module=account&action=txlist&address={$contract_address}&startblock=0&endblock=99999999&sort=asc&apikey={$etherscan_api_key}";
                    $options = ["http" => ["header" => "User-Agent: Mozilla/5.0\r\n"]];
                    $context = stream_context_create($options);
                    $tx_json = @file_get_contents($tx_url, false, $context);
                    $tx_data = $tx_json ? json_decode($tx_json, true) : null;

                    if ($tx_data && isset($tx_data['result']) && is_array($tx_data['result'])) {
                        foreach ($tx_data['result'] as $tx) {
                            $tx_time = isset($tx['timeStamp']) ? (int)$tx['timeStamp'] : 0;
                            $date = date('Y-m-d', $tx_time);
                            if (isset($tx_per_day[$date])) {
                                $tx_per_day[$date]++;
                            }
                        }
                    }
                }

                $tx_counts = array_values($tx_per_day);
                $total_transactions = array_sum($tx_counts);

                return $this->render('home.Admin.dashboard', compact(
                    'total_users',
                    'total_assets',
                    'total_orders',
                    'total_transactions',
                    'days',
                    'tx_counts',
                    'tx_data'
                ));
            }
            header('Location: /home');
            exit;
        }
        header('Location: /login');
        exit;
    }

    public function recentTransaction(): bool|array|string
    {
        AuthMiddleware::requireUser();
        return $this->render('home.User.recentTransaction');
    }

    public function orders(): bool|array|string
    {
        header('Location: /myRequests');
        exit;
    }

    public function qrScan(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if ($_SESSION['role'] == AuthMiddleware::ROLE_EMPLOYEE) {
                return $this->render('home.Employee.qrScan');
            }
            if ($_SESSION['role'] == AuthMiddleware::ROLE_ADMIN) {
                return $this->render('home.Admin.qrScan');
            }
            header('Location: /home');
            exit;
        }
        header('Location: /login');
        exit;
    }
}

