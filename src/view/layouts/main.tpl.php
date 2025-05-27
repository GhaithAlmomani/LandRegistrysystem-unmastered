<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= config('MVC.name') ?></title>
    <meta name="description" content="Description">
    <meta name="keywords" content="Keywords">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link href="<?= url('style/stylesheet/bootstrap.min.css'); ?>" rel="stylesheet">

    <link href="<?= url('style/stylesheet/main.css'); ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/web3/dist/web3.min.js"></script>
    <script src="<?= url('style/javascript/Abi.js'); ?>"></script>

</head>
<body>
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
<header class="header">
    <section class="flex">
        <div id="menu-btn" class="fas fa-bars" style="margin-right: 1.5rem; font-size: 1.7rem; cursor: pointer;"></div>
        <a href="/" class="logo">Department of Land and Survey</a>
        <form action="search" method="post" class="search-form">
            <input type="text" name="search_box" required placeholder="Search..." maxlength="100">
            <button type="submit" class="fas fa-search"></button>
        </form>
        <div class="icons">
            <div id="lang-btn" class="fa-solid fa-language"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="toggle-btn" class="fas fa-sun"></div>
        </div>
        <div class="profile profile-dropdown">
            <?php if ($userData): ?>
                <img src="<?= htmlspecialchars($userData['User_Avatar'] ?? 'images/pic-1.jpg') ?>" class="image" alt="">
                <h3 class="name"><?= htmlspecialchars($userData['User_Name']) ?></h3>
                <p class="role">
                    <?php
                        if ($userData['AdminID'] == 3) echo "Admin";
                        elseif ($userData['AdminID'] == 2) echo "Employee";
                        else echo "Individual";
                    ?>
                </p>
                <a href="profile" class="btn">View profile</a>
                <a href="logout" class="btn">Logout</a>
            <?php else: ?>
                <img src="images/pic-1.jpg" class="image" alt="">
                <h3 class="name">Guest</h3>
                <p class="role">Visitor</p>
                <div class="flex-btn">
                    <a href="login" class="option-btn">login</a>
                    <a href="register" class="option-btn">register</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</header>

<?php if (isset($content)): ?>
    <?= $content ?>
<?php endif; ?>

<?php if (empty($hiddenNavbar)) include_once 'navbar.tpl.php'; ?>
<div class="container">
    <div class="row">
        <div class="col">
            {{content}}
        </div>
    </div>
</div>
<?php include_once 'footer.tpl.php'; ?>
<script src="<?= url('style/javascript/bootstrap.bundle.min.js'); ?>"></script>
</body>

<script>

    let toggleBtn = document.getElementById('toggle-btn');
    let body = document.body;
    let darkMode = localStorage.getItem('dark-mode');

    const enableDarkMode = () =>{
        toggleBtn.classList.replace('fa-sun', 'fa-moon');
        body.classList.add('dark');
        localStorage.setItem('dark-mode', 'enabled');
    }

    const disableDarkMode = () =>{
        toggleBtn.classList.replace('fa-moon', 'fa-sun');
        body.classList.remove('dark');
        localStorage.setItem('dark-mode', 'disabled');
    }

    if(darkMode === 'enabled'){
        enableDarkMode();
    }

    toggleBtn.onclick = (e) =>{
        darkMode = localStorage.getItem('dark-mode');
        if(darkMode === 'disabled'){
            enableDarkMode();
        }else{
            disableDarkMode();
        }
    }

    let profile = document.querySelector('.header .flex .profile');

    document.querySelector('#user-btn').onclick = () =>{
        profile.classList.toggle('active');
        search.classList.remove('active');
    }

    let search = document.querySelector('.header .flex .search-form');

    document.querySelector('#search-btn').onclick = () =>{
        search.classList.toggle('active');
        profile.classList.remove('active');
    }

    let sideBar = document.querySelector('.side-bar');

    document.querySelector('#menu-btn').onclick = () =>{
        sideBar.classList.toggle('active');
        body.classList.toggle('active');
    }

    document.querySelector('#close-btn').onclick = () =>{
        sideBar.classList.remove('active');
        body.classList.remove('active');
    }

    window.onscroll = () =>{
        profile.classList.remove('active');
        search.classList.remove('active');

        if(window.innerWidth < 1200){
            sideBar.classList.remove('active');
            body.classList.remove('active');
        }
    }

</script>

<style>
.profile.profile-dropdown {
    position: absolute;
    top: 60px;
    right: 40px;
    min-width: 220px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    padding: 1.2rem 1.2rem 1rem 1.2rem;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: center;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-18px) scale(0.98);
    transition: opacity 0.25s cubic-bezier(.4,0,.2,1), transform 0.25s cubic-bezier(.4,0,.2,1);
}
.profile.profile-dropdown.active {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0) scale(1);
}
.profile.profile-dropdown .image {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #146C43;
    margin-bottom: 0.5rem;
}
.profile.profile-dropdown .name {
    font-size: 1.1em;
    font-weight: 600;
    color: #146C43;
    margin-bottom: 0.2rem;
}
.profile.profile-dropdown .role {
    font-size: 0.98em;
    color: #888;
    margin-bottom: 0.7rem;
}
.profile.profile-dropdown .btn,
.profile.profile-dropdown .option-btn {
    width: 100%;
    margin-bottom: 0.5rem;
    text-align: center;
}
.profile.profile-dropdown .flex-btn {
    width: 100%;
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}
</style>

</html>