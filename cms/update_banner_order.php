<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include("database/dbconnection.php");

$obj = new main();
$obj->connection();
sec_session_start();

if (login_check($mysqli) == true) {
    
    // Check if banner order data is provided
    if (isset($_POST["bannerOrder"]) && !empty($_POST["bannerOrder"])) {
        
        try {
            // Decode the JSON data
            $bannerOrder = json_decode($_POST["bannerOrder"], true);
            
            if (!$bannerOrder || !is_array($bannerOrder)) {
                echo json_encode(array("msg" => "Invalid banner order data", "response" => "E"));
                exit;
            }
            
            // Begin transaction for data consistency
            $mysqli->autocommit(FALSE);
            
            $success = true;
            $errorMessage = "";
            
            // Update each banner's position
            foreach ($bannerOrder as $item) {
                if (isset($item['bannerId']) && isset($item['position'])) {
                    $bannerId = intval($item['bannerId']);
                    $position = intval($item['position']);
                    
                    // Prepare and execute update statement
                    $stmt = $mysqli->prepare("UPDATE banners SET Position = ? WHERE BannerId = ?");
                    if ($stmt) {
                        $stmt->bind_param("ii", $position, $bannerId);
                        
                        if (!$stmt->execute()) {
                            $success = false;
                            $errorMessage = "Error updating banner ID " . $bannerId . ": " . $stmt->error;
                            break;
                        }
                        
                        $stmt->close();
                    } else {
                        $success = false;
                        $errorMessage = "Error preparing statement: " . $mysqli->error;
                        break;
                    }
                }
            }
            
            if ($success) {
                // Commit the transaction
                $mysqli->commit();
                echo json_encode(array(
                    "msg" => "Banner order updated successfully", 
                    "response" => "S",
                    "updated_count" => count($bannerOrder)
                ));
            } else {
                // Rollback the transaction
                $mysqli->rollback();
                echo json_encode(array(
                    "msg" => $errorMessage, 
                    "response" => "E"
                ));
            }
            
            // Restore autocommit
            $mysqli->autocommit(TRUE);
            
        } catch (Exception $e) {
            // Rollback on exception
            $mysqli->rollback();
            $mysqli->autocommit(TRUE);
            
            echo json_encode(array(
                "msg" => "Error processing banner order: " . $e->getMessage(), 
                "response" => "E"
            ));
        }
        
    } else {
        echo json_encode(array("msg" => "No banner order data provided", "response" => "E"));
    }
    
} else {
    echo json_encode(array("msg" => "Unauthorized access", "response" => "E"));
}
?>
