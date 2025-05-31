<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only regular users can access this page
AuthMiddleware::requireUser();

require_once __DIR__ . '/../../layouts/navbar.tpl.php';
?>

<section class="home-grid">

    <h1 class="heading">Quick options</h1>

    <div class="box-container">

        <!--<div class="box">
           <h3 class="title">likes and comments</h3>
           <p class="likes">total likes : <span>25</span></p>
           <a href="#" class="inline-btn">view likes</a>
           <p class="likes">total comments : <span>12</span></p>
           <a href="#" class="inline-btn">view comments</a>
           <p class="likes">saved playlists : <span>4</span></p>
           <a href="#" class="inline-btn">view playlists</a>
        </div> -->



        <div class="box">
            <h3 class="title">Sell and Registration</h3>
            <p class="tutor">Start selling or registering your assets.</p>
            <a href="sell" class="inline-btn">Start Now</a>
        </div>

        <div class="box">
            <h3 class="title">My Assets</h3>
            <p class="tutor">Review and manage your owned assets.</p>
            <a href="ownedassets" class="inline-btn">Review Now</a>
        </div>

        <div class="box">
            <h3 class="title">Orders</h3>
            <p class="tutor">Review the process of your application.</p>
            <a href="orders" class="inline-btn">Review Now</a>
        </div>

        <div class="box">
            <h3 class="title">Need a help?</h3>
            <p class="tutor">If you have any questions please: </p>
            <a href="contact" class="inline-btn">Contact us</a>
        </div>

        <div class="box">
            <h3 class="title">Top Services</h3>
            <div class="flex">
                <a href="#"><i class="fa-solid fa-globe"></i><span>Basic e-services</span></a>
                <a href="#"><i class="fas fa-chart-simple"></i><span>Quick e-services</span></a>
                <a href="#"><i class="fa-solid fa-magnifying-glass-location"></i><span>Locations</span></a>

            </div>
        </div>

        <div class="box">
            <h3 class="title">popular topics</h3>
            <div class="flex">
                <a href="#"><i class="fa-solid fa-share"></i><span>Sell</span></a>
                <a href="#"><i class="fa-solid fa-id-card"></i><span>Register</span></a>
                <a href="#"><i class="fa-solid fa-file"></i><span>Required documents</span></a>
            </div>
        </div>



    </div>

</section>
