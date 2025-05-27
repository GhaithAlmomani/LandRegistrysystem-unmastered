<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Username'])) {
    header('Location: login');
    exit();
}

// Connect to Database
$dsn = 'mysql:host=127.0.0.1;dbname=wise';
$user = 'root';
$pass = '994422Gg';
$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);

try {
    $con = new PDO($dsn, $user, $pass, $option);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Fetch user data
$stmt = $con->prepare("SELECT * FROM user WHERE User_Name = ?");
$stmt->execute([$_SESSION['Username']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    // User not found, force logout
    session_destroy();
    header('Location: login');
    exit();
}
?>
<section class="user-profile">

    <h1 class="heading">Your profile</h1>

    <div class="info">

        <div class="user">
            <img src="<?= htmlspecialchars($userData['User_Avatar'] ?? 'images/pic-6.jpg') ?>" alt="">
            <h3 class="name"><?= htmlspecialchars($userData['User_FullName']) ?></h3>
            <p class="role">
                <?php
                    if ($userData['AdminID'] == 3) echo "Admin";
                    elseif ($userData['AdminID'] == 2) echo "Employee";
                    else echo "Individual";
                ?>
            </p>
            <a href="update-profile" class="inline-btn">update profile</a>
        </div>

        <div class="box-container">

            <div class="box">
                <div class="flex">
                    <i class="fa-solid fa-house"></i>
                    <div>
                        <span>2</span>
                        <p>Assets owned</p>
                    </div>
                </div>
                <a href="ownedassets" class="inline-btn">View assets</a>
            </div>

            <div class="box">
                <div class="flex">
                    <i class="fa-solid fa-list-ul"></i>
                    <div>
                        <span>3</span>
                        <p>Orders</p>
                    </div>
                </div>
                <a href="#" class="inline-btn">View orders</a>
            </div>

            <div class="box">
                <div class="flex">
                    <i class="fa-solid fa-receipt"></i>
                    <div>
                        <span>12</span>
                        <p>Recent transaction</p>
                    </div>
                </div>
                <a href="recentTransaction" class="inline-btn">View transactions</a>
            </div>

        </div>
    </div>

</section>