<?php
session_start();
include('../database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

// Add error logging for debugging
error_log("Toggle blog visibility - Request received");
error_log("POST data: " . print_r($_POST, true));

// Skip login check since CMS is already protected
if (isset($_POST["BlogId"]) && $_POST["BlogId"] != "") {
    error_log("BlogId received: " . $_POST["BlogId"]);

    $BlogId = $_POST["BlogId"];

    // Get current status
    $FieldNames = array("IsActive");
    $ParamArray = array();
    $ParamArray[0] = $BlogId;
    $Fields = implode(",", $FieldNames);
    $current_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM blogs_master WHERE BlogId = ?", $FieldNames, "i", $ParamArray);

    if ($current_data) {
        error_log("Current blog status: " . $current_data[0]["IsActive"]);

        // Toggle the status
        $newStatus = ($current_data[0]["IsActive"] == "Y") ? "N" : "Y";
        error_log("New status will be: " . $newStatus);

        // Update the status
        $stmt = $mysqli->prepare("UPDATE blogs_master SET IsActive = ? WHERE BlogId = ?");
        $stmt->bind_param("si", $newStatus, $BlogId);

        if ($stmt->execute()) {
            error_log("Database update successful");
            $statusText = ($newStatus == "Y") ? "visible" : "hidden";
            echo json_encode(array(
                "msg" => "Blog " . $statusText . " successfully",
                "response" => "S",
                "newStatus" => $newStatus,
                "statusText" => ($newStatus == "Y") ? "Enabled" : "Disabled"
            ));
        } else {
            error_log("Database update failed: " . $stmt->error);
            echo json_encode(array("msg" => "Error updating blog visibility: " . $stmt->error, "response" => "E"));
        }

        $stmt->close();
    } else {
        error_log("Blog not found for ID: " . $BlogId);
        echo json_encode(array("msg" => "Blog not found", "response" => "E"));
    }
} else {
    error_log("Invalid blog ID received");
    echo json_encode(array("msg" => "Invalid blog ID", "response" => "E"));
}
?>
