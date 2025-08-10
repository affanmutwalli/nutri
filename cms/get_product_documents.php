<?php
ob_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();
sec_session_start();

header('Content-Type: application/json');

// if (login_check($mysqli) == true) {
if (true) { // Temporary bypass for testing
    if (isset($_GET['ProductId']) && !empty($_GET['ProductId'])) {
        try {
            $productId = intval($_GET['ProductId']);
            
            $stmt = $mysqli->prepare("SELECT document_id, document_title, document_type, file_name, file_path, file_size, mime_type, upload_date FROM product_documents WHERE product_id = ? AND is_active = 1 ORDER BY display_order ASC, upload_date DESC");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $documents = [];
            while ($row = $result->fetch_assoc()) {
                $documents[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'documents' => $documents
            ]);
            
            $stmt->close();
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
}
?>
