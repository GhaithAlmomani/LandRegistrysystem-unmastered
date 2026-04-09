<?php
?>

<section class="update-profile">
<style>
    .update-profile {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 20rem);
        background-color: var(--light-bg);
        padding: 2rem;
    }

    .update-profile form {
        background-color: var(--white);
        border-radius: .5rem;
        padding: 2.5rem;
        width: 100%;
        max-width: 40rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .update-profile form h3 {
        font-size: 2.8rem;
        color: var(--black);
        margin-bottom: 2rem;
        text-transform: capitalize;
    }

    .update-profile form p {
        font-size: 1.6rem;
        color: var(--black);
        text-align: left;
        margin-bottom: .8rem;
    }

    .update-profile form .box {
        width: 100%;
        padding: 1.4rem 1.6rem;
        font-size: 1.6rem;
        color: var(--black);
        background-color: var(--light-bg);
        border: var(--border);
        border-radius: .5rem;
        margin-bottom: 1.8rem;
        outline: none;
        transition: all 0.2s ease-in-out;
    }

    .update-profile form .box:focus {
        border-color: var(--main-color);
        box-shadow: 0 0 0 2px rgba(0, 86, 15, 0.2);
    }

    .update-profile form .btn {
        display: inline-block;
        background-color: var(--main-color);
        color: var(--white);
        padding: 1.2rem 2rem;
        font-size: 1.8rem;
        text-transform: capitalize;
        border-radius: .5rem;
        cursor: pointer;
        margin-top: 1rem;
        border: none;
        transition: all 0.3s ease;
        width: 100%;
    }

    .update-profile form .btn:hover {
        background-color: var(--black);
        color: var(--white);
    }

    @media (max-width: 768px) {
        .update-profile {
            padding: 1rem;
        }

        .update-profile form {
            padding: 2rem;
        }

        .update-profile form h3 {
            font-size: 2.4rem;
        }

        .update-profile form p {
            font-size: 1.4rem;
        }

        .update-profile form .box {
            font-size: 1.4rem;
        }

        .update-profile form .btn {
            font-size: 1.6rem;
        }
    }
</style>

    <form action="" method="post" enctype="multipart/form-data">
        <h3>Update profile</h3>
        <?php if ($error_message): ?>
            <div style="color:red; margin-bottom:10px;"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div style="color:green; margin-bottom:10px;"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <p>Update Username</p>
        <input type="text" name="name" placeholder="Username" maxlength="50" class="box" value="<?php echo htmlspecialchars($userData['User_Name'] ?? ''); ?>">
        <p>Update Email</p>
        <input type="email" name="email" placeholder="example@mail.com" maxlength="50" class="box" value="<?php echo htmlspecialchars($userData['User_Email'] ?? ''); ?>">
        <p>Previous Password</p>
        <input type="password" name="old_pass" placeholder="Old password" maxlength="20" class="box">
        <p>New Password</p>
        <input type="password" name="new_pass" placeholder="New password" maxlength="20" class="box">
        <p>Confirm Password</p>
        <input type="password" name="c_pass" placeholder="New password confirm" maxlength="20" class="box">
        <input type="submit" value="Submit" class="btn">
    </form>

</section>
