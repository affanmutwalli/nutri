<?php
session_start();

echo "<h2>Logout Debug Test</h2>";

echo "<h3>Before Logout:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "CustomerId in session: " . (isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : 'Not set') . "<br>";
echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Cookies:</h3>";
echo "Session cookie: " . (isset($_COOKIE[session_name()]) ? $_COOKIE[session_name()] : 'Not set') . "<br>";
echo "Custom session_id cookie: " . (isset($_COOKIE['session_id']) ? $_COOKIE['session_id'] : 'Not set') . "<br>";
echo "All cookies: <pre>" . print_r($_COOKIE, true) . "</pre>";

// Save cart to database before logout if user is logged in
if (isset($_SESSION['CustomerId'])) {
    echo "<h3>Saving cart to database...</h3>";
    try {
        include_once 'exe_files/cart_persistence.php';
        $cartManager = new CartPersistence();
        $cartManager->saveSessionCartToDatabase($_SESSION['CustomerId']);
        echo "Cart saved successfully.<br>";
    } catch (Exception $e) {
        echo "Cart save error: " . $e->getMessage() . "<br>";
    }
}

echo "<h3>Destroying session...</h3>";

// Get session parameters for proper cookie cleanup
$params = session_get_cookie_params();
echo "Session params: <pre>" . print_r($params, true) . "</pre>";

// Ensure the session is completely destroyed
session_unset();       // Unset all session variables
session_destroy();     // Destroy the session data

echo "Session unset and destroyed.<br>";

// Remove the session cookie with proper parameters
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    echo "Main session cookie cleared.<br>";
}

// Clear custom session_id cookie with proper parameters
if (isset($_COOKIE['session_id'])) {
    setcookie('session_id', '', time() - 3600, '/', '', true, true);
    echo "Custom session_id cookie cleared.<br>";
}

// Clear any other potential session cookies
$cookiesToClear = ['PHPSESSID', 'session_id', 'remember_me'];
foreach ($cookiesToClear as $cookieName) {
    if (isset($_COOKIE[$cookieName])) {
        setcookie($cookieName, '', time() - 3600, '/');
        setcookie($cookieName, '', time() - 3600, '/', '');
        echo "Cookie '$cookieName' cleared.<br>";
    }
}

echo "<h3>After Logout:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "CustomerId in session: " . (isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : 'Not set') . "<br>";

echo "<br><a href='login.php'>Go to Login Page</a><br>";
echo "<a href='index.php'>Go to Home Page</a><br>";
echo "<a href='check_session.php'>Check Session Status</a><br>";

?>
