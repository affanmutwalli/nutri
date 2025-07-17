<?php
// Simple logout without cart persistence (for immediate logout)
session_start();

// Get session parameters for proper cookie cleanup
$params = session_get_cookie_params();

// Clear all session variables first
$_SESSION = array();

// Remove the session cookie with proper parameters
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Clear custom session_id cookie (this is critical for the custom session system)
if (isset($_COOKIE['session_id'])) {
    setcookie('session_id', '', time() - 3600, '/', '', true, true);
    // Also try without secure flag in case of HTTP
    setcookie('session_id', '', time() - 3600, '/');
}

// Clear any other potential session cookies with multiple variations
$cookiesToClear = ['PHPSESSID', 'session_id', 'remember_me'];
foreach ($cookiesToClear as $cookieName) {
    if (isset($_COOKIE[$cookieName])) {
        // Try multiple variations to ensure cookie is cleared
        setcookie($cookieName, '', time() - 3600, '/');
        setcookie($cookieName, '', time() - 3600, '/', '');
        setcookie($cookieName, '', time() - 3600, '/', '', false, false);
        setcookie($cookieName, '', time() - 3600, '/', '', true, true);
    }
}

// Destroy the session after clearing cookies
session_destroy();

// Start a new clean session to prevent any issues
session_start();
session_regenerate_id(true);

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

// Add a small delay to ensure cookies are processed
usleep(100000); // 0.1 second delay

// Redirect to the login page
header("Location: login.php?logout=success");
exit;
?>
