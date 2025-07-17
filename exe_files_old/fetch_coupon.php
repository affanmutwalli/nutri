<?php
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');


$obj = new main();
$obj->connection();

sec_session_start();

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Decode JSON data from request
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['code']) && !empty($data['code'])) {
            $code = htmlspecialchars($data['code']);
            
            $FieldNames = array("CodeId", "Code", "Discount");
            $ParamArray = array($code);
            $Fields = implode(",", $FieldNames);
            $result = $obj->MysqliSelect1("SELECT " . $Fields . " FROM coupons WHERE Code = ?", $FieldNames, "s", $ParamArray);

            if (!empty($result)) {
                $discount = $result[0]['Discount'];
                echo json_encode([
                    "msg" => "Coupon applied successfully.",
                    "response" => "S",
                    "discount" => $discount
                ]);
            } else {
                echo json_encode([
                    "msg" => "The Code Is Invalid or Expired",
                    "response" => "E"
                ]);
            }
        } else {
            echo json_encode([
                "msg" => "Please Enter Code",
                "response" => "E"
            ]);
        }
    } else {
        echo json_encode([
            "msg" => "Invalid request method.",
            "response" => "E"
        ]);
    }
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    echo json_encode([
        "msg" => "An internal error occurred.",
        "response" => "E"
    ]);
}
?>
