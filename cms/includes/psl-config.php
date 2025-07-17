<?php
/**
 * These are the database login details
 */
define("HOST", "localhost"); // Use the domain or IP of the remote server
define("USER", "root"); // Your remote database username
define("PASSWORD", ""); // Your remote database password
define("DATABASE", "my_nutrify_db"); // Your remote database name

//define("USER", "tastytre_nirvana1");
//define("PASSWORD", "[V4LWeC5Ccb6");
//define("DATABASE", "tastytre_nirvana1");

define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");

define("SECURE", FALSE);

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

