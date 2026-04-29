<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userData = $currentUser ?? null;
?>

<div class="side-bar">
    <div id="close-btn">
        <i class="fas fa-times"></i>
    </div>

    <nav class="navbar">
        <section class="profile-section">
        <?php if ($userData): ?>
            <img src="<?= htmlspecialchars($userData['User_Avatar'] ?? 'images/pic-1.jpg') ?>" class="image" alt="">
            <h3 class="name"><?= htmlspecialchars($userData['User_Name']) ?></h3>
            <p class="role">
                <?php
                    echo \MVC\middleware\AuthMiddleware::getRoleName($userData['AdminID']);
                ?>
            </p>
            <a href="profile" class="btn">View Profile</a>
        <?php else: ?>
            <img src="images/pic-1.jpg" class="image" alt="">
            <h3 class="name">Guest</h3>
            <p class="role">Visitor</p>
            <div class="flex-btn">
                <a href="login" class="option-btn">login</a>
                <a href="register" class="option-btn">register</a>
            </div>
        <?php endif; ?>
        </section>
        
        <div class="nav-links">
            <a href="home"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="about"><i class="fas fa-question"></i><span>About</span></a>
            <a href="learn-more"><i class="fas fa-chalkboard-user"></i><span>Learn More</span></a>
            <a href="contact"><i class="fas fa-headset"></i><span>Contact us</span></a>
            <?php if (isset($userData) && $userData['AdminID'] == \MVC\middleware\AuthMiddleware::ROLE_ADMIN): ?>
                <a href="search"><i class="fas fa-search"></i><span>User Search</span></a>
            <?php endif; ?>
        </div>
    </nav>
    <?php if (isset($userData)): ?>
        <a href="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    <?php endif; ?>
</div>