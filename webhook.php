<?php
// webhook.php

$jsonData = file_get_contents('php://input'); // Read the raw POST data
$data = json_decode($jsonData, true); // Decode JSON data

// Log the webhook payload (for debugging purposes)
file_put_contents('webhook_log.txt', print_r($data, true), FILE_APPEND);

// Process the data (you can handle it as needed)
if ($data['result'] == true) {
    // Message sent successfully, perform actions
    // You can store delivery status or update your system here
}
?>
