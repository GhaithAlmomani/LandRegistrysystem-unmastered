<?php
?>

<section class="form-container register-page" aria-labelledby="register-heading">
    <div class="register-page-shell">
        <a href="<?= htmlspecialchars(url('')) ?>" class="register-page-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <span>Back to home</span>
        </a>

        <form action="" method="post" enctype="multipart/form-data" class="register-form">
            <header class="register-form-brand">
                <div class="register-form-brand-icon" aria-hidden="true">
                    <i class="fas fa-user-plus"></i>
                </div>
                <p class="register-form-eyebrow">Create an account</p>
                <h3 id="register-heading" class="register-form-title">Register as user</h3>
                <p class="register-form-lead">Complete the form below to open a portal account with the Department of Land and Survey.</p>
            </header>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <div class="register-form-fields">
                <label class="register-field">
                    <span class="register-field-label">Full name <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="text" name="name" placeholder="Enter your full name" required maxlength="50" class="box" autocomplete="name" value="<?= isset($_POST['name']) ? htmlspecialchars((string) $_POST['name']) : '' ?>">
                </label>
                <label class="register-field">
                    <span class="register-field-label">Username <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="text" name="username" placeholder="Enter your username" required minlength="8" maxlength="50" class="box" autocomplete="username" value="<?= isset($_POST['username']) ? htmlspecialchars((string) $_POST['username']) : '' ?>">
                </label>
                <label class="register-field">
                    <span class="register-field-label">Email address <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="email" name="email" placeholder="Enter your email address" required maxlength="50" class="box" autocomplete="email" value="<?= isset($_POST['email']) ? htmlspecialchars((string) $_POST['email']) : '' ?>">
                </label>
                <label class="register-field">
                    <span class="register-field-label">Password <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="password" name="pass" placeholder="Enter your password" required minlength="8" maxlength="20" class="box" autocomplete="new-password">
                </label>
                <label class="register-field">
                    <span class="register-field-label">Confirm password <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="password" name="c_pass" placeholder="Confirm your password" required minlength="8" maxlength="20" class="box" autocomplete="new-password">
                </label>
                <label class="register-field">
                    <span class="register-field-label">Phone number <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="tel" name="phonenumber" placeholder="Enter your phone number" required maxlength="20" class="box" autocomplete="tel" value="<?= isset($_POST['phonenumber']) ? htmlspecialchars((string) $_POST['phonenumber']) : '' ?>">
                </label>
                <label class="register-field">
                    <span class="register-field-label">National ID <span class="register-required" aria-hidden="true">*</span></span>
                    <input type="text" name="ID" placeholder="Enter your ID number" required inputmode="numeric" maxlength="20" class="box" autocomplete="off" value="<?= isset($_POST['ID']) ? htmlspecialchars((string) $_POST['ID']) : '' ?>">
                </label>
            </div>

            <button type="submit" name="submit" value="1" class="btn register-form-submit">
                <i class="fas fa-user-check" aria-hidden="true"></i>
                Register as user
            </button>

            <section class="box register-form-footer" aria-labelledby="register-login-prompt">
                <h4 id="register-login-prompt">Already have an account?</h4>
                <a href="<?= htmlspecialchars(url('login')) ?>" class="btn btn-outline register-form-login">Sign in</a>
            </section>
        </form>
    </div>
</section>
