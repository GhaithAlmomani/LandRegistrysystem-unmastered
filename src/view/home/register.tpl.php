<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to home
if (isset($_SESSION['Username'])) {
    header('Location: home');
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
    error_log("Database connection successful");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die('Connection failed: ' . $e->getMessage());
}

$error_message = '';
$success_message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    error_log("Form submitted");
    
    // Get form data and sanitize inputs
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $pass = $_POST['pass'];
    $c_pass = $_POST['c_pass'];
    $phonenumber = filter_var(trim($_POST['phonenumber']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $national_id = filter_var(trim($_POST['ID']), FILTER_SANITIZE_NUMBER_INT);

    // Debug: Print received data
    error_log("Received registration data: " . print_r($_POST, true));

    // Validate inputs
    if (empty($name) || empty($username) || empty($email) || empty($pass) || empty($c_pass) || empty($phonenumber) || empty($national_id)) {
        $error_message = 'All fields are required!';
        error_log("Validation error: Empty fields");
    } elseif (!$email) {
        $error_message = 'Invalid email format!';
        error_log("Validation error: Invalid email");
    } elseif (strlen($username) < 8) {
        $error_message = 'Username must be at least 8 characters long!';
        error_log("Validation error: Username too short");
    } elseif (strlen($pass) < 8) {
        $error_message = 'Password must be at least 8 characters long!';
        error_log("Validation error: Password too short");
    } elseif ($pass !== $c_pass) {
        $error_message = 'Passwords do not match!';
        error_log("Validation error: Passwords do not match");
    } else {
        try {
            // Check if username already exists
            $stmt = $con->prepare('SELECT COUNT(*) FROM user WHERE User_Name = ?');
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = 'Username already exists!';
                error_log("Error: Username already exists");
            } else {
                // Check if email already exists
                $stmt = $con->prepare('SELECT COUNT(*) FROM user WHERE User_Email = ?');
                $stmt->execute([$email]);
                if ($stmt->fetchColumn() > 0) {
                    $error_message = 'Email already exists!';
                    error_log("Error: Email already exists");
                } else {
                    // Check if national ID already exists
                    $stmt = $con->prepare('SELECT COUNT(*) FROM user WHERE User_NationalID = ?');
                    $stmt->execute([$national_id]);
                    if ($stmt->fetchColumn() > 0) {
                        $error_message = 'National ID already registered!';
                        error_log("Error: National ID already exists");
                    } else {
                        // Get the next User_Number
                        $stmt = $con->query('SELECT MAX(User_Number) as max_num FROM user');
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $next_user_number = ($result['max_num'] ?? 0) + 1;
                        error_log("Next user number: " . $next_user_number);

                        // Insert new user with AdminID = 1 (regular user only)
                        $hashed_password = hash('sha256', $pass);
                        
                        // Debug: Print the SQL query and parameters
                        error_log("Attempting to insert user with number: " . $next_user_number);
                        
                        // Prepare statement with the correct columns from the database schema
                        $sql = 'INSERT INTO user (
                            User_Number,
                            User_Name,
                            User_FullName,
                            User_Password,
                            User_Email,
                            User_Avatar,
                            failed_login,
                            date,
                            AdminID,
                            SourceID,
                            User_Phone,
                            User_NationalID
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)';
                        
                        error_log("SQL Query: " . $sql);
                        
                        $stmt = $con->prepare($sql);
                        
                        $params = [
                            $next_user_number,
                            $username,
                            $name,
                            $hashed_password,
                            $email,
                            'images/pic-6.jpg', // Default avatar
                            0, // failed_login
                            1, // AdminID = 1 for regular users
                            0, // SourceID
                            $phonenumber,
                            $national_id
                        ];
                        
                        // Debug: Print the parameters
                        error_log("SQL Parameters: " . print_r($params, true));
                        
                        try {
                            if ($stmt->execute($params)) {
                                $new_user_id = $con->lastInsertId();
                                error_log("User successfully inserted with ID: " . $new_user_id);
                                
                                // Verify the insertion
                                $verify = $con->query("SELECT * FROM user WHERE User_ID = " . $new_user_id);
                                $user_data = $verify->fetch(PDO::FETCH_ASSOC);
                                error_log("Verified user data: " . print_r($user_data, true));
                                
                                $success_message = 'Registration successful! Redirecting to login...';
                                header("Location: login");
                                exit();
                            } else {
                                $error_info = $stmt->errorInfo();
                                error_log("Failed to insert user. Error info: " . print_r($error_info, true));
                                $error_message = 'Registration failed. Please try again.';
                            }
                        } catch (PDOException $e) {
                            error_log("Execute error: " . $e->getMessage());
                            $error_message = 'Registration failed: Database error.';
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error_message = 'Registration failed: Database error.';
        }
    }
}
?>

<section class="form-container">
    <?php if ($error_message): ?>
        <div class="error-message" style="color: red; margin-bottom: 15px; padding: 10px; background-color: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="success-message" style="color: green; margin-bottom: 15px; padding: 10px; background-color: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 4px;">
            <?php echo htmlspecialchars($success_message); ?>
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