<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only employees can access this page
AuthMiddleware::requireEmployee();

require_once __DIR__ . '/../../components/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>Employee Dashboard</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Property Registration</h5>
                            <p class="card-text">Register new properties in the system</p>
                            <a href="/propertyRegistration" class="btn btn-primary">Register Property</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Property Transfer</h5>
                            <p class="card-text">Handle property transfer requests</p>
                            <a href="/propertyTransfer" class="btn btn-primary">Transfer Property</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">QR Scanner</h5>
                            <p class="card-text">Scan QR codes for property verification</p>
                            <a href="/qrScan" class="btn btn-primary">Scan QR</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">All Properties</h5>
                            <p class="card-text">View all registered properties</p>
                            <a href="/allProperties" class="btn btn-primary">View Properties</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Property Information</h5>
                            <p class="card-text">View detailed property information</p>
                            <a href="/PropertyInfo" class="btn btn-primary">View Info</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Property Count</h5>
                            <p class="card-text">View property statistics</p>
                            <a href="/PropertyCount" class="btn btn-primary">View Count</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../components/footer.php';
?> 