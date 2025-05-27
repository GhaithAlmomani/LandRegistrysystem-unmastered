<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

$error_message = '';
$success_message = '';

// Assume you store the username in session as 'Username'
if (!isset($_SESSION['Username'])) {
    header('Location: login');
    exit();
}

// Fetch user data
$stmt = $con->prepare("SELECT * FROM user WHERE User_Name = ?");
$stmt->execute([$_SESSION['Username']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    $error_message = "User not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $c_pass = $_POST['c_pass'];

    // Validate email
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // If password fields are filled, check old password and update
        if (!empty($old_pass) && !empty($new_pass) && !empty($c_pass)) {
            if (hash('sha256', $old_pass) !== $userData['User_Password']) {
                $error_message = "Old password is incorrect.";
            } elseif ($new_pass !== $c_pass) {
                $error_message = "New passwords do not match.";
            } elseif (strlen($new_pass) < 8) {
                $error_message = "New password must be at least 8 characters.";
            } else {
                // Update name, email, and password
                $stmt = $con->prepare("UPDATE user SET User_Name = ?, User_Email = ?, User_Password = ? WHERE User_ID = ?");
                $stmt->execute([$new_name, $new_email, hash('sha256', $new_pass), $userData['User_ID']]);
                $success_message = "Profile and password updated successfully.";
                $_SESSION['Username'] = $new_name;
            }
        } else {
            // Update only name and email
            $stmt = $con->prepare("UPDATE user SET User_Name = ?, User_Email = ? WHERE User_ID = ?");
            $stmt->execute([$new_name, $new_email, $userData['User_ID']]);
            $success_message = "Profile updated successfully.";
            $_SESSION['Username'] = $new_name;
        }
        // Refresh user data
        $stmt = $con->prepare("SELECT * FROM user WHERE User_ID = ?");
        $stmt->execute([$userData['User_ID']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
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
