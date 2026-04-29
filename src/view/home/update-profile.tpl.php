<?php
?>

<section class="admin-page profile-update-page">
    <header class="card profile-hero profile-hero--compact">
        <div class="profile-hero__top">
            <div class="profile-hero__identity">
                <div class="profile-hero__meta">
                    <h1 class="heading profile-hero__title">Update profile</h1>
                    <div class="profile-hero__sub">
                        <span class="profile-hero__hint">Keep your account details current for notifications and transactions.</span>
                    </div>
                </div>
            </div>
            <div class="profile-hero__actions">
                <a href="profile" class="inline-btn">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Back
                </a>
            </div>
        </div>
    </header>

    <div class="card admin-page-card">
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="" method="post" class="admin-page-form">
            <?= \MVC\core\CSRFToken::generateFormField() ?>

            <label class="admin-page-label" for="name">Username</label>
            <input id="name" type="text" name="name" placeholder="Username" maxlength="50" class="box"
                   value="<?= htmlspecialchars($userData['User_Name'] ?? ''); ?>">
            <p class="admin-page-help">This name appears on your profile and some receipts.</p>

            <label class="admin-page-label" for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="example@mail.com" maxlength="50" class="box"
                   value="<?= htmlspecialchars($userData['User_Email'] ?? ''); ?>">
            <p class="admin-page-help">We use email for important notifications.</p>

            <div class="profile-update-split">
                <div>
                    <label class="admin-page-label" for="old_pass">Previous password</label>
                    <input id="old_pass" type="password" name="old_pass" placeholder="Old password" maxlength="64" class="box">
                </div>
                <div>
                    <label class="admin-page-label" for="new_pass">New password</label>
                    <input id="new_pass" type="password" name="new_pass" placeholder="New password" maxlength="64" class="box">
                </div>
                <div>
                    <label class="admin-page-label" for="c_pass">Confirm new password</label>
                    <input id="c_pass" type="password" name="c_pass" placeholder="Confirm new password" maxlength="64" class="box">
                </div>
            </div>
            <p class="admin-page-help">Leave password fields empty if you only want to update name/email.</p>

            <div class="admin-page-actions">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Save changes
                </button>
            </div>
        </form>
    </div>
</section>
