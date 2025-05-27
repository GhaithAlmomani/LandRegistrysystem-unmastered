<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only regular users can access this page
AuthMiddleware::requireUser();

require_once __DIR__ . '/../../components/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>User Dashboard</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">My Properties</h5>
                            <p class="card-text">View your owned properties</p>
                            <a href="/ownedassets" class="btn btn-primary">View Properties</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sell Property</h5>
                            <p class="card-text">List your property for sale</p>
                            <a href="/sell" class="btn btn-primary">Sell Property</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sell Requests</h5>
                            <p class="card-text">View your property sale requests</p>
                            <a href="/sellReq" class="btn btn-primary">View Requests</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Recent Transactions</h5>
                            <p class="card-text">View your recent property transactions</p>
                            <a href="/recentTransaction" class="btn btn-primary">View Transactions</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Profile Settings</h5>
                            <p class="card-text">Update your profile information</p>
                            <a href="/update-profile" class="btn btn-primary">Update Profile</a>
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