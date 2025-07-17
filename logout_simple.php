<?php
session_start();

// Simple logout without cart persistence (for testing)
echo "Logging out...<br>";

// Ensure the session is completely destroyed
session_unset();       // Unset all session variables
session_destroy();     // Destroy the session data

// Remove the session cookie, if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Clear custom session_id cookie
if (isset($_COOKIE['session_id'])) {
    setcookie('session_id', '', time() - 3600, '/');
}

echo "Session destroyed. Redirecting...<br>";

// Redirect to the login page
header("Location: login.php");
exit;
?>
