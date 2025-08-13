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
if (!isset($_POST['product_id']) || !isset($_POST['section_title']) || !isset($_POST['section_content'])) {
    echo json_encode(array('success' => false, 'message' => 'Required fields are missing'));
    exit();
}

$product_id = intval($_POST['product_id']);
$section_title = trim($_POST['section_title']);
$section_content = trim($_POST['section_content']);
$section_type = isset($_POST['section_type']) ? $_POST['section_type'] : 'custom';
$display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;

// Validate inputs
if ($product_id <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Invalid product ID'));
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
    // Check if product exists
    $product_check_query = "SELECT ProductId FROM product_master WHERE ProductId = ?";
    $product_exists = $obj->fSelectNew($product_check_query, "i", array($product_id));
    
    if (empty($product_exists)) {
        echo json_encode(array('success' => false, 'message' => 'Product not found'));
        exit();
    }
    
    // If display_order is 0, set it to the next available order
    if ($display_order == 0) {
        $max_order_query = "SELECT COALESCE(MAX(display_order), 0) + 1 as next_order FROM product_about_sections WHERE product_id = ?";
        $max_order_result = $obj->fSelectNew($max_order_query, "i", array($product_id));
        $display_order = !empty($max_order_result) ? $max_order_result[0]['next_order'] : 1;
    }
    
    // Insert the new about section
    $ParamArray = array($product_id, $section_title, $section_content, $section_type, $display_order);
    $result = $obj->fInsertNew("INSERT INTO product_about_sections (product_id, section_title, section_content, section_type, display_order, is_active, created_date) VALUES (?, ?, ?, ?, ?, 1, NOW())", "isssi", $ParamArray);
    
    if ($result) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'About section added successfully',
            'section_id' => $result
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to save about section'));
    }
    
} catch (Exception $e) {
    error_log("Save about section error: " . $e->getMessage());
    echo json_encode(array('success' => false, 'message' => 'Database error: ' . $e->getMessage()));
}
?>
