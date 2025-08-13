<?php
session_start();
include_once '../database/dbconnection.php';
include_once '../functions.php';

$obj = new dbconnection();

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (login_check($mysqli) == false) {
    echo json_encode(array('success' => false, 'message' => 'Unauthorized access'));
    exit();
}

// Check if section_id is provided
if (!isset($_POST['section_id']) || empty($_POST['section_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Section ID is required'));
    exit();
}

$section_id = intval($_POST['section_id']);

if ($section_id <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Invalid section ID'));
    exit();
}

try {
    // Get section data
    $section_query = "SELECT * FROM product_about_sections WHERE section_id = ?";
    $section_data = $obj->fSelectNew($section_query, "i", array($section_id));
    
    if (empty($section_data)) {
        echo json_encode(array('success' => false, 'message' => 'Section not found'));
        exit();
    }
    
    echo json_encode(array('success' => true, 'data' => $section_data[0]));
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => 'Database error: ' . $e->getMessage()));
}
?>
