<?php ob_start(); ?>
<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();
ini_set('max_execution_time', 300);

sec_session_start();

if (login_check($mysqli) == true) 
{
    // Use "d-m-y" format for date display
    $Date = date("Y-m-d");
    
    if (!empty($_POST["Id"])) // Checking if ID is present
    {
        $stmt = $mysqli->prepare("UPDATE metal_rates SET Date = ?, 18K = ?, 22K = ?, 24K = ?, Silver = ? WHERE Id = ?");
        $stmt->bind_param("sssssi", $Date, $_POST["18K"], $_POST["22K"], $_POST["24K"], $_POST["Silver"], $_POST["Id"]);
        $stmt->execute();
        $stmt->close();

        $_SESSION["QueryStatus"] = "UPDATED";
        echo json_encode(array("msg" => "Record updated successfully", "response" => "S"));   
    }
    else
    {
        $ParamArray = array();
        $ParamArray[0] = $Date;
        $ParamArray[1] = $_POST["18K"];
        $ParamArray[2] = $_POST["22K"];
        $ParamArray[3] = $_POST["24K"];
        $ParamArray[4] = $_POST["Silver"];

        // Use appropriate "sssss" for string types in bind_param
        $InputDocId = $obj->fInsertNew("INSERT INTO metal_rates (Date, 18K, 22K, 24K, Silver) VALUES (?, ?, ?, ?, ?)", "sssss", $ParamArray);

        $_SESSION["QueryStatus"] = "SAVED";

        echo json_encode(array("msg" => "Record saved successfully", "response" => "S"));   
    }
}
?>
