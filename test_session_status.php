<?php
session_start();

echo "<h2>Session Status Check</h2>";

echo "<h3>Current Session:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "CustomerId in session: " . (isset($_SESSION['CustomerId']) ? $_SESSION['CustomerId'] : 'Not set') . "<br>";
echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Cookies:</h3>";
echo "Session cookie: " . (isset($_COOKIE[session_name()]) ? $_COOKIE[session_name()] : 'Not set') . "<br>";
echo "Custom session_id cookie: " . (isset($_COOKIE['session_id']) ? $_COOKIE['session_id'] : 'Not set') . "<br>";
echo "All cookies: <pre>" . print_r($_COOKIE, true) . "</pre>";

echo "<h3>Login Status:</h3>";
if(isset($_SESSION["CustomerId"]) && !empty($_SESSION["CustomerId"])) {
    echo "Status: LOGGED IN<br>";
    echo "Customer ID: " . $_SESSION["CustomerId"] . "<br>";
} else {
    echo "Status: NOT LOGGED IN<br>";
}

echo "<br><a href='test_logout.php'>Test Logout</a><br>";
echo "<a href='logout.php'>Regular Logout</a><br>";
echo "<a href='login.php'>Go to Login Page</a><br>";
echo "<a href='index.php'>Go to Home Page</a><br>";

?>
