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

// Check if required fields are provided
if (!isset($_POST['section_id']) || !isset($_POST['section_title']) || !isset($_POST['section_content'])) {
    echo json_encode(array('success' => false, 'message' => 'Required fields are missing'));
    exit();
}

$section_id = intval($_POST['section_id']);
$section_title = trim($_POST['section_title']);
$section_content = trim($_POST['section_content']);
$section_type = isset($_POST['section_type']) ? $_POST['section_type'] : 'custom';
$display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
$is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;

// Validate inputs
if ($section_id <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Invalid section ID'));
    exit();
}

if (empty($section_title)) {
    echo json_encode(array('success' => false, 'message' => 'Section title is required'));
    exit();
}

if (empty($section_content)) {
    echo json_encode(array('success' => false, 'message' => 'Section content is required'));
    exit();
}

// Validate section type
$valid_types = array('benefits', 'usage', 'ingredients', 'specifications', 'safety', 'faq', 'custom');
if (!in_array($section_type, $valid_types)) {
    $section_type = 'custom';
}

try {
    // Check if section exists
    $section_check_query = "SELECT section_id FROM product_about_sections WHERE section_id = ?";
    $section_exists = $obj->fSelectNew($section_check_query, "i", array($section_id));
    
    if (empty($section_exists)) {
        echo json_encode(array('success' => false, 'message' => 'Section not found'));
        exit();
    }
    
    // Update the about section
    $update_query = "UPDATE product_about_sections SET 
        section_title = ?, 
        section_content = ?, 
        section_type = ?, 
        display_order = ?, 
        is_active = ?,
        updated_date = NOW()
    WHERE section_id = ?";
    
    $result = $obj->fUpdateNew($update_query, "sssiii", array(
        $section_title, 
        $section_content, 
        $section_type, 
        $display_order, 
        $is_active, 
        $section_id
    ));
    
    if ($result !== false) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'About section updated successfully'
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to update about section'));
    }
    
} catch (Exception $e) {
    error_log("Update about section error: " . $e->getMessage());
    echo json_encode(array('success' => false, 'message' => 'Database error occurred'));
}
?>
