<?php


//IF You Are Login:
if (isset($_SESSION['Username'])) {
    header('Location: home');
    exit();
}
?>


<section class="form-container">

    <form action="" method="post" enctype="multipart/form-data">
        <h3>login Now</h3>
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <p>Username<span>*</span></p>
        <input type="text" name="username" placeholder="Enter your Username" required minlength="8" maxlength="50" class="box">
        <p>Password<span>*</span></p>
        <input type="password" name="password" placeholder="Enter your Password" required minlength="8" maxlength="20" class="box">
        <input type="submit" value="Login" name="Login" class="btn">

        <section class="box">
            <h4>Don't have an account?</h4>
            <a href="register" class="btn">Register</a>
        </section>

    </form>



</section>