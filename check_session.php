<?php
session_start();

// Check if the user is logged in
if(isset($_SESSION["CustomerId"]) && !empty($_SESSION["CustomerId"])) {
    echo json_encode(['loggedIn' => true]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
