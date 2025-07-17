<?php
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');
// Adjust this to your DB connection file
$obj = new main();
$mysqli = $obj->connection();

$query = $_GET['query'] ?? '';
$response = [];

if ($query) {
    $FieldNames = ["ProductId", "ProductName"];
    $Fields = implode(",", $FieldNames);

    // Use prepared statements to prevent SQL injection
    $stmt = $obj->prepare("SELECT $Fields FROM product_master WHERE ProductName LIKE CONCAT('%', ?, '%') LIMIT 10");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode($response);
