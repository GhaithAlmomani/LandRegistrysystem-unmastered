<style>
    .side-bar {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    .side-bar .navbar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
        padding: 1rem;
    }
    .navbar .nav-links {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        width: 100%;
    }
    .logout-btn {
        color: #217a36; /* green text */
        border: 1.5px solid #217a36; /* green border */
        padding: 0.9rem 1.5rem;
        border-radius: 0.7rem;
        transition: all 0.2s cubic-bezier(.4,2,.6,1);
        width: calc(100% - 2rem);
        text-align: center;
        background: #fff;
        margin: 1rem;
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
        cursor: pointer;
        outline: none;
        gap: 0.5rem;
    }
    .logout-btn:hover {
        background: #ff4444;
        color: #fff;
        border-color: #ff4444;
    }
    .logout-btn i {
        margin-right: 0.5rem;
        font-size: 1.2em;
        display: flex;
        align-items: center;
    }
</style>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userData = null;
if (isset($_SESSION['Username'])) {
    $dsn = 'mysql:host=127.0.0.1;dbname=wise';
    $user = 'root';
    $pass = '994422Gg';
    $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
    try {
        $con = new PDO($dsn, $user, $pass, $option);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $con->prepare("SELECT * FROM user WHERE User_Name = ?");
        $stmt->execute([$_SESSION['Username']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $userData = null;
    }
}
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
                    if ($userData['AdminID'] == 3) echo "Admin";
                    elseif ($userData['AdminID'] == 2) echo "Employee";
                    else echo "individual";
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
            <?php if (isset($userData) && $userData['AdminID'] == 3): ?>
                <a href="search"><i class="fas fa-search"></i><span>User Search</span></a>
            <?php endif; ?>
        </div>
    </nav>
    <?php if (isset($userData)): ?>
        <a href="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    <?php endif; ?>
</div>