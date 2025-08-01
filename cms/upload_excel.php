<?php
ob_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

sec_session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Code']) && !empty($_POST['Code'])) {
    // Ensure the code starts with '#' symbol
    $code = trim($_POST['Code']);
    if ($code[0] !== '#') {
        $code = '#' . $code; // Prepend # if not already present
    }

    // Prepare for querying the database (with proper field names and parameters)
    $FieldNames = array("CodeId", "Code");
    $ParamArray = array($code); // Param for Code (assuming Code is a string)

    // SQL query to fetch CodeId and Code
    $Fields = implode(",", $FieldNames);
    $product_price = $obj->MysqliSelect1("SELECT " . $Fields . " FROM verify_product WHERE Code = ?", $FieldNames, "s", $ParamArray);

    // Check if the product price is found (not empty)
    if ($product_price) {
        $_SESSION["QueryStatus"] = "SAVED";
        echo json_encode(["msg" => "Genuine Product.", "response" => "S"]);
    } else {
        $_SESSION["QueryStatus"] = "DELETED";
        echo json_encode(["msg" => "The Code Is Invalid, No Product Found", "response" => "E"]);
    }
} else {
    $_SESSION["QueryStatus"] = "UPDATED";
    echo json_encode(["msg" => "Please Enter Code", "response" => "E"]);
}
?>
