<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only admins can access this page
AuthMiddleware::requireAdmin();

require_once __DIR__ . '/../../layouts/navbar.tpl.php';
?>

<section class="home-grid">

    <h1 class="heading">Admin Portal</h1>

    <div class="box-container">

    <div class="box">
            <h3 class="title">Admin Dashboard</h3>
            <p class="tutor">View analytics, smart contract transactions, and system statistics in the admin dashboard.</p>
            <a href="dashboard" class="inline-btn">Go to Dashboard</a>
        </div>
        
        <div class="box">
            <h3 class="title">Set Employees Authorization</h3>
            <p class="tutor">Assign and manage authorization employees to control access within the system.</p>
            <a href="setEmpAuth" class="inline-btn">Set Authorization</a>
        </div>

        <div class="box">
            <h3 class="title">Check Authorized Employees</h3>
            <p class="tutor">Review and verify the authorized employees to ensure proper access and permissions.</p>
            <a href="checkEmpAuth" class="inline-btn">Check Authorization</a>
        </div>

        <div class="box">
            <h3 class="title">Scan QR-Code</h3>
            <p class="tutor">Scan the QR code to easily set or update employee addresses for accurate records and management.</p>
            <a href="qrScan" class="inline-btn">Scan Now</a>
        </div>

        <div class="box">
            <h3 class="title">Check The Admin</h3>
            <p class="tutor">Verify and review admin address within the system for security and management purposes.</p>
            <a href="adminAddress" class="inline-btn">Review Now</a>
        </div>

    </div>

</section>

