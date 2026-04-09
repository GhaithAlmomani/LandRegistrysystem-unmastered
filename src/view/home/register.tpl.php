<?php
?>

<section class="form-container">
    <?php if ($error_message): ?>
        <div class="error-message" style="color: red; margin-bottom: 15px; padding: 10px; background-color: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <h3>Register as User</h3>
        <p>Full name <span>*</span></p>
        <input type="text" name="name" placeholder="Enter your full name" required maxlength="50" class="box" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        
        <p>Username <span>*</span></p>
        <input type="text" name="username" placeholder="Enter your username" required minlength="8" maxlength="50" class="box" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        
        <p>Email address<span>*</span></p>
        <input type="email" name="email" placeholder="Enter your email address" required maxlength="50" class="box" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        
        <p>Password <span>*</span></p>
        <input type="password" name="pass" placeholder="Enter your password" required minlength="8" maxlength="20" class="box">
        
        <p>Confirm password <span>*</span></p>
        <input type="password" name="c_pass" placeholder="Confirm your password" required minlength="8" maxlength="20" class="box">

        <p>Phone number <span>*</span></p>
        <input type="tel" name="phonenumber" placeholder="Enter your phone number" required maxlength="20" class="box" value="<?php echo isset($_POST['phonenumber']) ? htmlspecialchars($_POST['phonenumber']) : ''; ?>">
        
        <p>National ID<span>*</span></p>
        <input type="number" name="ID" placeholder="Enter your ID number" required maxlength="20" class="box" value="<?php echo isset($_POST['ID']) ? htmlspecialchars($_POST['ID']) : ''; ?>">

        <input type="submit" value="Register as User" name="submit" class="btn">

        <section class="box">
            <h4>Already have an account?</h4>
            <a href="login" class="btn">login</a>
        </section>
    </form>
</section>