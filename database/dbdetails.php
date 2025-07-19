<?php
/**
 * These are the database login details
 * Using conditional defines to prevent duplicate constant errors
 */
if (!defined("HOST")) {
    define("HOST", "localhost"); // Use the domain or IP of the remote server
}
if (!defined("USER")) {
    define("USER", "root"); // Your remote database username
}
if (!defined("PASSWORD")) {
    define("PASSWORD", ""); // Your remote database password
}
if (!defined("DATABASE")) {
    define("DATABASE", "my_nutrify_db"); // Your remote database name
}

//define("USER", "tastytre_nirvana1");
//define("PASSWORD", "[V4LWeC5Ccb6");
//define("DATABASE", "tastytre_nirvana1");

if (!defined("CAN_REGISTER")) {
    define("CAN_REGISTER", "any");
}
if (!defined("DEFAULT_ROLE")) {
    define("DEFAULT_ROLE", "member");
}
if (!defined("SECURE")) {
    define("SECURE", FALSE);
}
?>