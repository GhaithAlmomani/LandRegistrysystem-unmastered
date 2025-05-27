<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only employees can access this page
AuthMiddleware::requireEmployee();

require_once __DIR__ . '/../../layouts/navbar.tpl.php';
?>

<section class="home-grid">

    <h1 class="heading">Employee Portal</h1>

    <div class="box-container">

        <div class="box">
            <h3 class="title">Register Property</h3>
            <p class="tutor">Easily register the property for secure and verified transactions.</p>
            <a href="propertyRegistration" class="inline-btn">Register Now</a>
        </div>

        <div class="box">
            <h3 class="title">Property Transfer</h3>
            <p class="tutor">Transfer ownership of your property with ease and legal security.</p>
            <a href="propertyTransfer" class="inline-btn">Transfer Now</a>
        </div>

        <div class="box">
            <h3 class="title">Scan QR-Code</h3>
            <p class="tutor">Scan the QR code to verify property owner and ensure secure transactions.</p>
            <a href="qrScan" class="inline-btn">Scan Now</a>
        </div>

        <div class="box">
            <h3 class="title">Get All Properties</h3>
            <p class="tutor">Browse and explore all registered properties in the system.</p>
            <a href="allProperties" class="inline-btn">Review Now</a>
        </div>

        <div class="box">
            <h3 class="title">Get Property by ID</h3>
            <p class="tutor">Browse and explore all registered properties in the system by ID.</p>
            <a href="PropertyById" class="inline-btn">View by ID</a>
        </div>

        <div class="box">
            <h3 class="title">Get All Properties Count</h3>
            <p class="tutor">View the total count of all properties registered and managed within the system.</p>
            <a href="PropertyCount" class="inline-btn">View Count</a>
        </div>

        <div class="box">
            <h3 class="title">Get All Properties Info</h3>
            <p class="tutor">Access the detailed count of all properties information for better management.</p>
            <a href="PropertyInfo" class="inline-btn">View Information</a>
        </div>

        <div class="box">
            <h3 class="title">Sell Requests</h3>
            <p class="tutor">View and process property transfer requests from sellers.</p>
            <a href="sellRequest" class="inline-btn">View Requests</a>
        </div>

    </div>

</section>
