<?php
ob_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();
ini_set('max_execution_time', 300);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['CategoryId'])) {
    $CategoryId = $_POST['CategoryId'];
        
    $FieldNames = array("SubCategoryId", "SubCategoryName");
    $ParamArray = array($CategoryId);  // assuming "1" is a placeholder and should be dynamically set or validated
    $Fields = implode(",", $FieldNames);
    // Prepare the SQL statement
    $sub_categories = $obj->MysqliSelect1("SELECT $Fields FROM sub_category WHERE CategoryId = ?", $FieldNames, "i", $ParamArray);
    
    if (!empty($sub_categories)) {
        foreach ($sub_categories as $sub_category) {
            echo '<option value="' . htmlspecialchars($sub_category['SubCategoryId']) . '">' . htmlspecialchars($sub_category['SubCategoryName']) . '</option>';
        }
    } else {
        echo '<option value="">No Subcategories Available</option>';
    }
}
?>
