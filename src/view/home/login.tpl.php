<?php


//IF You Are Login:
if (isset($_SESSION['Username'])) {
    header('Location: home');
    exit();
}
?>


<section class="form-container login-page" aria-labelledby="login-heading">
    <div class="login-page-shell">
        <a href="<?= htmlspecialchars(url('')) ?>" class="login-page-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <span>Back to home</span>
        </a>

        <form action="" method="post" enctype="multipart/form-data" class="login-form">
            <header class="login-form-brand">
                <div class="login-form-brand-icon" aria-hidden="true">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <p class="login-form-eyebrow">Secure access</p>
                <h3 id="login-heading" class="login-form-title">Sign in</h3>
                <p class="login-form-lead">Use your registered username and password to access the land registry portal.</p>
            </header>

            <?php if (!empty($error)): ?>
                <div class="error-message" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="login-form-fields">
                <label class="login-field">
                    <span class="login-field-label">Username <span class="login-required" aria-hidden="true">*</span></span>
                    <input type="text" name="username" placeholder="Enter your username" required minlength="8" maxlength="50" class="box" autocomplete="username" inputmode="text">
                </label>
                <label class="login-field">
                    <span class="login-field-label">Password <span class="login-required" aria-hidden="true">*</span></span>
                    <input type="password" name="password" placeholder="Enter your password" required minlength="8" maxlength="20" class="box" autocomplete="current-password">
                </label>
            </div>

            <button type="submit" name="Login" value="Login" class="btn login-form-submit">
                <i class="fas fa-right-to-bracket" aria-hidden="true"></i>
                Sign in
            </button>

            <section class="box login-form-footer" aria-labelledby="login-register-prompt">
                <h4 id="login-register-prompt">New to the portal?</h4>
                <a href="<?= htmlspecialchars(url('register')) ?>" class="btn btn-outline login-form-register">Create an account</a>
            </section>
        </form>
    </div>
</section>
