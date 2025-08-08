<?php
// Initialize banner positions with sequential values
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

try {
    // Get all banners ordered by BannerId
    $FieldNames = array("BannerId");
    $ParamArray = array();
    $Fields = implode(",",$FieldNames);
    $banner_data = $obj->MysqliSelect1("Select ".$Fields." from banners ORDER BY BannerId ASC",$FieldNames,"s",$ParamArray);
    
    if ($banner_data && count($banner_data) > 0) {
        // Begin transaction
        $mysqli->autocommit(FALSE);
        
        $success = true;
        $updated_count = 0;
        
        // Update each banner with sequential position
        foreach ($banner_data as $index => $banner) {
            $bannerId = $banner["BannerId"];
            $position = $index; // 0, 1, 2, 3, etc.
            
            $stmt = $mysqli->prepare("UPDATE banners SET Position = ? WHERE BannerId = ?");
            if ($stmt) {
                $stmt->bind_param("ii", $position, $bannerId);
                
                if ($stmt->execute()) {
                    $updated_count++;
                } else {
                    $success = false;
                    break;
                }
                
                $stmt->close();
            } else {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            $mysqli->commit();
            echo json_encode(array(
                "msg" => "Successfully initialized positions for $updated_count banners", 
                "response" => "S",
                "updated_count" => $updated_count
            ));
        } else {
            $mysqli->rollback();
            echo json_encode(array(
                "msg" => "Error initializing banner positions", 
                "response" => "E"
            ));
        }
        
        $mysqli->autocommit(TRUE);
        
    } else {
        echo json_encode(array(
            "msg" => "No banners found to initialize", 
            "response" => "E"
        ));
    }
    
} catch (Exception $e) {
    $mysqli->rollback();
    $mysqli->autocommit(TRUE);
    
    echo json_encode(array(
        "msg" => "Error: " . $e->getMessage(), 
        "response" => "E"
    ));
}
?>
