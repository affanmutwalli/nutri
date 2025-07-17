<?php
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve JSON input
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    // Check if required fields are present
    if (isset($data['customerName']) && !empty($data['customerName'])
        && isset($data['customerEmail']) && !empty($data['customerEmail'])
        && isset($data['customerPhone']) && !empty($data['customerPhone'])) {

        // Sanitize input data
        $name = htmlspecialchars(trim($data['customerName']));
        $email = htmlspecialchars($data['customerEmail']);
        $mobile_no = htmlspecialchars($data['customerPhone']);
        $address = htmlspecialchars($data['shippingAddress'] ?? '');
        $city = htmlspecialchars($data['city'] ?? '');
        $pincode = htmlspecialchars($data['pincode'] ?? '');
        $state = htmlspecialchars($data['state'] ?? '');

        // Validate mobile number
        if (strlen($mobile_no) < 10) {
            echo json_encode(["msg" => "Invalid mobile number.", "response" => "E"]);
            exit;
        }

        $OTP = rand(100000, 999999); // Generate OTP

        // Check if customer already exists
        // $checkQuery = "SELECT * FROM direct_customers WHERE Email = ? OR MobileNo = ?";
        // $stmt = $mysqli->prepare($checkQuery);
        // $stmt->bind_param("ss", $email, $mobile_no);
        // $stmt->execute();
        // $result = $stmt->get_result();

        if (isset($data['customerEmail']) && isset($data['customerEmail'])) {
            // Insert new customer data
            $ParamArray = array($name, $email, $mobile_no, $address, $city, $pincode, $state, $OTP);
            $InputDocId = $obj->fInsertNew(
                "INSERT INTO direct_customers (CustomerName, Email, MobileNo, Address, City, Pincode, State, OTP) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                "ssssssss",
                $ParamArray
            );

            if ($InputDocId) {
                echo json_encode(["msg" => "OTP Sent Successfully.", "response" => "S", "success" => true]);
            } else {
                echo json_encode(["msg" => "Error: Could not register customer.", "response" => "E", "success" => false]);
            }
        } else {
            echo json_encode(["msg" => "Mobile Number Or Email Already Exist.", "response" => "E", "success" => false]);
        }
    } else {
        echo json_encode(["msg" => "Please provide all required fields.", "response" => "E", "success" => false]);
    }
} else {
    echo json_encode(["msg" => "Invalid request method.", "response" => "E", "success" => false]);
}
?>
