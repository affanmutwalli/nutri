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
        // Check if ProductId is provided
        if (!isset($_POST["ProductId"]) || empty($_POST["ProductId"])) {
            throw new Exception('Product ID is required');
        }
        
        $productId = $_POST["ProductId"];
        
        // Validate that productId is numeric
        if (!is_numeric($productId)) {
            throw new Exception('Invalid product ID');
        }
        
        // First, fetch all images for this product to delete the physical files
        $FieldNames = array("ImageId", "PhotoPath");
        $ParamArray = array($productId);
        $Fields = implode(",", $FieldNames);
        $all_images = $obj->MysqliSelect1(
            "SELECT $Fields FROM model_images WHERE ProductId = ?",
            $FieldNames,
            "i",
            $ParamArray
        );
        
        if (empty($all_images)) {
            echo json_encode(array(
                "success" => true,
                "message" => "No images found to delete",
                "response" => "S"
            ));
            exit;
        }
        
        // Begin transaction
        $obj->mysqli->begin_transaction();
        
        $deletedFiles = 0;
        $deletedRecords = 0;
        
        // Delete physical files first
        foreach ($all_images as $image) {
            if (!empty($image["PhotoPath"])) {
                $filename = $_SERVER['DOCUMENT_ROOT'] . "/images/products/" . $image["PhotoPath"];
                
                // Check if the file exists and delete it
                if (file_exists($filename)) {
                    if (unlink($filename)) {
                        $deletedFiles++;
                    }
                }
            }
        }
        
        // Delete all database records for this product
        $ParamArray = array($productId);
        $result = $obj->fDeleteNew(
            "DELETE FROM model_images WHERE ProductId = ?",
            "i",
            $ParamArray
        );
        
        if ($result) {
            $deletedRecords = count($all_images);
            
            // Commit transaction
            $obj->mysqli->commit();
            
            $_SESSION["QueryStatus"] = "DELETED";
            
            echo json_encode(array(
                "success" => true,
                "message" => "Successfully deleted $deletedRecords images ($deletedFiles files removed from disk)",
                "response" => "D",
                "deleted_records" => $deletedRecords,
                "deleted_files" => $deletedFiles
            ));
        } else {
            // Rollback transaction
            $obj->mysqli->rollback();
            
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to delete images from database",
                "response" => "E"
            ));
        }
        
    } catch (Exception $e) {
        // Rollback transaction if it was started
        if (isset($obj->mysqli)) {
            $obj->mysqli->rollback();
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
