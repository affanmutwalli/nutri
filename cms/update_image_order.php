<?php
// Clean output buffer and set headers
ob_clean();
header('Content-Type: application/json');

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();
ini_set('max_execution_time', 300);

sec_session_start();

if (login_check($mysqli) == true) {
    try {
        // Get the JSON data from the request
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['imageOrder']) || !isset($data['productId'])) {
            throw new Exception('Invalid data received');
        }
        
        $imageOrder = $data['imageOrder'];
        $productId = $data['productId'];
        
        // Validate that productId is numeric
        if (!is_numeric($productId)) {
            throw new Exception('Invalid product ID');
        }
        
        // Get database connection
        $mysqli = $obj->connection();

        // Begin transaction
        $mysqli->begin_transaction();

        // Update each image's sort_order
        $updateSuccess = true;
        foreach ($imageOrder as $index => $imageId) {
            if (!is_numeric($imageId)) {
                throw new Exception('Invalid image ID: ' . $imageId);
            }

            $sortOrder = $index + 1; // Start from 1

            // Use direct mysqli prepared statement for better control
            $stmt = $mysqli->prepare("UPDATE model_images SET sort_order = ? WHERE ImageId = ? AND ProductId = ?");
            $stmt->bind_param("iii", $sortOrder, $imageId, $productId);
            $result = $stmt->execute();
            $stmt->close();

            if (!$result) {
                $updateSuccess = false;
                break;
            }
        }
        
        if ($updateSuccess) {
            // Commit transaction
            $mysqli->commit();
            echo json_encode(array(
                "success" => true,
                "message" => "Image order updated successfully",
                "response" => "S"
            ));
        } else {
            // Rollback transaction
            $mysqli->rollback();
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to update image order",
                "response" => "E"
            ));
        }
        
    } catch (Exception $e) {
        // Rollback transaction if it was started
        if (isset($mysqli)) {
            $mysqli->rollback();
        }

        echo json_encode(array(
            "success" => false,
            "message" => "Error: " . $e->getMessage(),
            "response" => "E"
        ));
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Unauthorized access",
        "response" => "E"
    ));
}
?>
