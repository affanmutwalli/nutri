<?php
// Start session to access session data
session_start();

// Save cart to database before logout if user is logged in
if (isset($_SESSION['CustomerId'])) {
    try {
        // Try to include cart persistence with proper error handling
        if (file_exists('exe_files/cart_persistence.php')) {
            include_once 'exe_files/cart_persistence.php';
            if (class_exists('CartPersistence')) {
                $cartManager = new CartPersistence();
                $cartManager->saveSessionCartToDatabase($_SESSION['CustomerId']);
            }
        } else {
            // Alternative: Direct database save without cart persistence class
            include_once 'database/dbconnection.php';
            $obj = new main();
            $obj->connection();

            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                // Simple cart save logic
                foreach ($_SESSION['cart'] as $productId => $quantity) {
                    $obj->fInsertNew(
                        "INSERT INTO cart (CustomerId, ProductId, Quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE Quantity = ?",
                        "iiii",
                        array($_SESSION['CustomerId'], $productId, $quantity, $quantity)
                    );
                }
            }
        }
    } catch (Exception $e) {
        // Log error but don't prevent logout
        error_log("Cart save error during logout: " . $e->getMessage());
    } catch (Error $e) {
        // Log fatal errors but don't prevent logout
        error_log("Cart save fatal error during logout: " . $e->getMessage());
    }
}

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
