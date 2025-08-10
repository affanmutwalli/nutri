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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['document_id'])) {
                echo json_encode(['success' => false, 'message' => 'Document ID is required']);
                exit;
            }
            
            $documentId = intval($input['document_id']);
            
            // Get file path before deleting from database
            $stmt = $mysqli->prepare("SELECT file_path FROM product_documents WHERE document_id = ?");
            $stmt->bind_param("i", $documentId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $filePath = $row['file_path'];

                // Delete from database
                $deleteStmt = $mysqli->prepare("DELETE FROM product_documents WHERE document_id = ?");
                $deleteStmt->bind_param("i", $documentId);
                
                if ($deleteStmt->execute()) {
                    // Delete physical file
                    $fullFilePath = __DIR__ . '/' . $filePath;
                    if (file_exists($fullFilePath)) {
                        unlink($fullFilePath);
                    }

                    echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $deleteStmt->error]);
                }
                
                $deleteStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Document not found']);
            }
            
            $stmt->close();
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
}
?>
