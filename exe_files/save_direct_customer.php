<?php
header('Content-Type: application/json');

// Path to the JSON file
$jsonFilePath = 'customer_data.json';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = file_get_contents("php://input");

    // Decode the JSON data into a PHP associative array
    $decodedData = json_decode($data, true);

    // Check if decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid JSON format"
        ]);
        exit;
    }

    // Load existing data from the JSON file
    $existingData = [];
    if (file_exists($jsonFilePath)) {
        $existingData = json_decode(file_get_contents($jsonFilePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $existingData = []; // Reset if the file has invalid JSON
        }
    }

    // Append the new data
    $existingData[] = $decodedData;

    // Save the updated data back to the file
    if (file_put_contents($jsonFilePath, json_encode($existingData, JSON_PRETTY_PRINT))) {
        echo json_encode([
            "success" => true,
            "message" => "Data saved successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to save data"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
}
?>
