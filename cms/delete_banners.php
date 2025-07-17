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

if (login_check($mysqli) == true) {
    // Check if DBannerId is set
    if (!empty($_POST["DBannerId"])) {
        
        // Fetch the PhotoPath for the given DBannerId
        $FieldNames = array("PhotoPath");
        $ParamArray = array($_POST["DBannerId"]);
        $Fields = implode(",", $FieldNames);
        $single_data = $obj->MysqliSelect1("SELECT $Fields FROM banners WHERE BannerId = ?", $FieldNames, "i", $ParamArray);

        if (!empty($single_data) && isset($single_data[0]["PhotoPath"])) {
            // Construct full file path
            $photoPath = $single_data[0]["PhotoPath"];
            $filename = $_SERVER['DOCUMENT_ROOT'] . "/images/banners/" . $photoPath;

            // Check if the file exists and delete it
            if (file_exists($filename)) {
                unlink($filename); // Delete the file
            }
        }

        // Proceed to delete the record from the database
        $ParamArray = array($_POST["DBannerId"]);
        $ContestId = $obj->fDeleteNew("DELETE FROM banners WHERE BannerId = ?", "i", $ParamArray);

        // Set session status and respond with success message
        $_SESSION["QueryStatus"] = "DELETED";
        echo json_encode(array("msg" => "Record deleted successfully", "response" => "D"));
    }
}
?>
