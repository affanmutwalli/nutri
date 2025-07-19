<?php
session_start();
header('Content-Type: application/json');

require_once '../database/dbconnection.php';

// Response array
$response = array('success' => false, 'message' => '');

try {
    // Simple admin check
    if (!isset($_SESSION['admin_logged_in'])) {
        if (!isset($_SESSION['CustomerId'])) {
            throw new Exception('Access denied');
        }
    }
    
    // Check if form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $message_id = (int)($_POST['message_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    
    // Validation
    if ($message_id <= 0) {
        throw new Exception('Invalid message ID');
    }
    
    if (!in_array($status, ['new', 'read', 'replied'])) {
        throw new Exception('Invalid status');
    }
    
    // Initialize database connection
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception('Database connection failed');
    }
    
    // Update message status
    $stmt = $mysqli->prepare("UPDATE contact_messages SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $status, $message_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update status: ' . $stmt->error);
    }
    
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    if ($affected_rows === 0) {
        throw new Exception('Message not found or status unchanged');
    }
    
    $response['success'] = true;
    $response['message'] = 'Status updated successfully';
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Contact status update error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
?>
