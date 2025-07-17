<?php
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start(); // Our custom secure way of starting a PHP session.

if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.

    // Debug logging
    error_log("Login attempt for email: " . $email);
    error_log("Password hash length: " . strlen($password));

    if (login($email, $password, $mysqli) == true) {
        // Login success
        error_log("Login successful for: " . $email);
        header('Location: ../dashboard.php');
    } else {
        // Login failed
        error_log("Login failed for: " . $email);
        header('Location: ../index.php?error=1');
    }
} else {
    // The correct POST variables were not sent to this page.
    error_log("Invalid login request - missing email or password");
    echo 'Invalid Request - Missing email or password fields';
}
?>