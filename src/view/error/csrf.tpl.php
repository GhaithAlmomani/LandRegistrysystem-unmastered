<?php
$title = 'CSRF Error';
$content = '
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Security Error</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">CSRF Token Validation Failed</h5>
                    <p class="card-text">
                        The security token for your request has expired or is invalid. This is a security measure to protect against cross-site request forgery attacks.
                    </p>
                    <p class="card-text">
                        Please try the following:
                    </p>
                    <ul>
                        <li>Refresh the page and try again</li>
                        <li>Make sure you\'re not using an expired form</li>
                        <li>Clear your browser cache and try again</li>
                    </ul>
                    <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
                    <a href="/" class="btn btn-secondary">Go to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>
'; 