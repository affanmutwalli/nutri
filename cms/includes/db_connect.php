<?php
include_once 'psl-config.php';   // As functions.php is not included

// Create connection with error handling
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

// Check connection
if ($mysqli->connect_error) {
    error_log("Database connection failed: " . $mysqli->connect_error);
    die("Connection failed: " . $mysqli->connect_error . " - Please check database credentials in psl-config.php");
}

// Set charset to utf8
$mysqli->set_charset("utf8");
?>