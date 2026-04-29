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
    <script>
        // Add CSRF token to all forms
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            const csrfToken = '<?= \MVC\core\CSRFToken::getToken() ?>';
            
            forms.forEach(form => {
                if (!form.querySelector('input[name="csrf_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }
            });

            // Add CSRF token to all AJAX requests
            const originalFetch = window.fetch;
            window.fetch = function() {
                let [resource, config] = arguments;
                if (config === undefined) {
                    config = {};
                }
                if (config.headers === undefined) {
                    config.headers = {};
                }
                config.headers['X-CSRF-TOKEN'] = csrfToken;
                return originalFetch(resource, config);
            };
        });
    </script>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userData = $currentUser ?? null;
?>
<header class="header">
    <section class="flex">
        <div id="menu-btn" class="fas fa-bars"></div>
        <a href="/" class="logo">Department of Land and Survey</a>
        <form action="search" method="post" class="search-form">
            <input type="text" name="search_box" required placeholder="Search..." maxlength="100">
            <button type="submit" class="fas fa-search"></button>
        </form>
        <div class="icons">
            <div id="lang-btn" class="fa-solid fa-language"></div>
            <div id="wallet-btn" class="fa-brands fa-ethereum" title="Connect Wallet" aria-label="Connect Wallet"></div>
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
                        echo \MVC\middleware\AuthMiddleware::getRoleName($userData['AdminID']);
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

<div id="wallet-toast" class="wallet-toast" role="status" aria-live="polite" hidden></div>

<?php if (empty($hiddenNavbar)) include_once 'navbar.tpl.php'; ?>
<main class="main-content" id="main">
    <div class="container">
        <div class="row">
            <div class="col">
                {{content}}
            </div>
        </div>
    </div>
</main>
<?php include_once 'footer.tpl.php'; ?>
<script src="<?= url('style/javascript/bootstrap.bundle.min.js'); ?>"></script>
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

    // Header: Connect Wallet (MetaMask) without navigation
    const walletBtn = document.getElementById('wallet-btn');
    const walletToast = document.getElementById('wallet-toast');
    const showWalletToast = (msg, kind) => {
        if (!walletToast) return;
        walletToast.hidden = false;
        walletToast.className = 'wallet-toast' + (kind ? ` ${kind}` : '');
        walletToast.textContent = msg;
        clearTimeout(window.__walletToastTimer);
        window.__walletToastTimer = setTimeout(() => { walletToast.hidden = true; }, 4000);
    };

    async function connectWalletInline() {
        if (typeof window.ethereum === 'undefined') {
            showWalletToast('MetaMask not detected. Redirecting to download…', 'is-warning');
            setTimeout(() => { window.location.href = 'https://metamask.io/download.html'; }, 700);
            return;
        }
        try {
            showWalletToast('Connecting wallet…', null);
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            const account = accounts?.[0] ?? '';
            if (!account) {
                showWalletToast('No account returned from wallet.', 'is-warning');
                return;
            }
            const shortAcc = account.substring(0, 6) + '...' + account.substring(account.length - 4);
            showWalletToast(`Connected: ${shortAcc}`, 'is-success');
        } catch (e) {
            showWalletToast('Connection cancelled or failed.', 'is-error');
        }
    }

    if (walletBtn) {
        walletBtn.onclick = () => connectWalletInline();
    }

</script>
</body>
</html>