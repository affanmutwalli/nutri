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

// Get product ID from query parameter
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Valid product ID is required'));
    exit();
}

try {
    // Get about sections for the product
    $sections_query = "SELECT 
        section_id,
        product_id,
        section_title,
        section_content,
        section_type,
        display_order,
        is_active,
        created_date,
        updated_date
    FROM product_about_sections 
    WHERE product_id = ? 
    ORDER BY display_order ASC, section_id ASC";
    
    $sections = $obj->fSelectNew($sections_query, "i", array($product_id));
    
    if ($sections === false) {
        echo json_encode(array('success' => false, 'message' => 'Error retrieving sections'));
        exit();
    }
    
    // Return sections data
    echo json_encode(array(
        'success' => true,
        'sections' => $sections ? $sections : array(),
        'total_sections' => $sections ? count($sections) : 0
    ));
    
} catch (Exception $e) {
    error_log("Get about sections error: " . $e->getMessage());
    echo json_encode(array('success' => false, 'message' => 'Database error occurred'));
}
?>
