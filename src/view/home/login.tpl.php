<?php


//IF You Are Login:
if (isset($_SESSION['Username'])) {
    header('Location: home');
    exit();
}




//Connect Databases:
$dsn 	= 'mysql:host=127.0.0.1;dbname=wise';
$user 	= 'root';
$pass 	= '994422Gg';
$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
try{
    $con = new PDO($dsn, $user, $pass, $option);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e){
    echo 'Failed To connected' . $e->getMessage();
}


if( isset( $_POST[ 'Login' ] ) && isset ($_POST['username']) && isset ($_POST['password']) ) {

        $username = $_POST['username'];
        $password = hash('sha256', $_POST['password']);

    $data = $con->prepare('SELECT failed_login, last_login FROM User WHERE User_Name = (:user) LIMIT 1;');
    $data->bindParam( ':user', $username, PDO::PARAM_STR );
    $data->execute();
    $row = $data->fetch();

    $total_failed_login = 30;
    $lockout_time		= 1;
    $account_locked		= false;

    if( ( $data->rowCount() == 1 ) && ( $row[ 'failed_login' ] >= $total_failed_login ) )  {

        $last_login = strtotime( $row[ 'last_login' ] );
        $timeout 	= $last_login + ($lockout_time * 60);
        $timenow 	= time();

        if( $timenow < $timeout ) {
            $account_locked = true;
        }
    }
    $data = $con->prepare( 'SELECT * FROM User WHERE User_Name = (:user) AND User_Password = (:password) LIMIT 1;' );
    $data->bindParam( ':user', $username, PDO::PARAM_STR);
    $data->bindParam( ':password', $password, PDO::PARAM_STR );
    $data->execute();
    $row = $data->fetch();

    if( ($data->rowCount() == 1) && ($account_locked == false) ){
        $data = $con->prepare( 'UPDATE User SET failed_login = "0" WHERE User_Name = (:user) LIMIT 1;' );
        $data->bindParam( ':user', $username, PDO::PARAM_STR );
        $data->execute();
        $_SESSION['Username'] 	= $username;
        $_SESSION['role'] 	= $row[ 'AdminID' ];
        header('Location: home');
        exit();
    } else {
        // Login failed
        sleep( rand(2,4) );
        // Update bad login count
        $data = $con->prepare( 'UPDATE User SET failed_login = (failed_login + 1) WHERE User_Name = (:user) LIMIT 1;' );
        $data->bindParam( ':user', $username, PDO::PARAM_STR );
        $data->execute();
        // exit To Login
        header("refresh: 0");
        exit;
    }
    // Set the last login time
    $data = $con->prepare( 'UPDATE User SET last_login = now() WHERE User_Name = (:user) LIMIT 1;' );
    $data->bindParam( ':user', $username, PDO::PARAM_STR );
    $data->execute();
}
?>


<section class="form-container">

    <form action="" method="post" enctype="multipart/form-data">
        <h3>login Now</h3>
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