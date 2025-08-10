<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();
sec_session_start();

header('Content-Type: application/json');

// For testing, we'll temporarily bypass login check
// if (login_check($mysqli) == true) {
if (true) { // Temporary bypass for testing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Debug: Log received data
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            // Validate required fields
            if (empty($_POST['ProductId']) || empty($_POST['DocumentTitle']) || empty($_FILES['DocumentFile']['name'])) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }

            $productId = intval($_POST['ProductId']);
            $documentTitle = trim($_POST['DocumentTitle']);
            $documentType = $_POST['DocumentType'] ?? 'lab_report';
            
            // Validate file
            $file = $_FILES['DocumentFile'];
            $allowedTypes = ['application/pdf'];
            $maxFileSize = 10 * 1024 * 1024; // 10MB
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'File upload error']);
                exit;
            }
            
            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed']);
                exit;
            }
            
            if ($file['size'] > $maxFileSize) {
                echo json_encode(['success' => false, 'message' => 'File size must be less than 10MB']);
                exit;
            }
            
            // Create upload directory structure
            $baseDir = 'docs/products/' . $documentType . '/';
            $fullPath = __DIR__ . '/' . $baseDir;

            if (!file_exists($fullPath)) {
                if (!mkdir($fullPath, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                    exit;
                }
            }

            // Generate unique filename
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'product_' . $productId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = $baseDir . $fileName;
            $fullFilePath = $fullPath . '/' . $fileName;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $fullFilePath)) {
                // Save to database
                $stmt = $mysqli->prepare("INSERT INTO product_documents (product_id, document_title, document_type, file_name, file_path, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssis", $productId, $documentTitle, $documentType, $file['name'], $filePath, $file['size'], $file['type']);

                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Document uploaded successfully',
                        'document_id' => $mysqli->insert_id
                    ]);
                } else {
                    // Delete uploaded file if database insert fails
                    if (file_exists($fullFilePath)) {
                        unlink($fullFilePath);
                    }
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload file. Check directory permissions.']);
            }
            
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
