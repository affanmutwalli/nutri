<?php
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!empty($_POST["email"])) {
    $Email = $_POST['email'];
    $Password = $_POST['password'];

    $FieldNames = array("CustomerId", "Email", "Pass", "OTP", "IsActive");
    $ParamArray = array();
    $ParamArray[0] = $Email;
    $Fields = implode(",", $FieldNames);

    // Fetch data from database
    $single_data = $obj->MysqliSelect1("Select " . $Fields . " from customer_master where Email= ?", $FieldNames, "s", $ParamArray);

    if (!empty($single_data)) {
        // Verify the password
        if (password_verify($Password, $single_data[0]["Pass"])) {
            if ($single_data[0]["IsActive"] === "Y") {
                
                // Generate a secure session identifier (token)
                $sessionId = bin2hex(random_bytes(32)); // Secure random session ID

                // Store the session ID in the session and associate it with the CustomerId
                $_SESSION["session_id"] = $sessionId;
                $_SESSION["CustomerId"] = $single_data[0]["CustomerId"];
                
                // Store the session ID in a secure, HttpOnly cookie (optional)
                setcookie("session_id", $sessionId, time() + (30 * 24 * 60 * 60), "/", "", true, true); // 30 days

                // Optionally, store additional information (e.g., user-agent or IP) to prevent session hijacking
                $_SESSION["UserAgent"] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION["IP"] = $_SERVER['REMOTE_ADDR'];

                echo json_encode(array("msg" => "Login Successful", "response" => "S"));
                exit();
            } else {
                echo json_encode(array("msg" => "Account Not Verified, Please Verify From Registration Form", "response" => "E"));
                exit();
            }
        } else {
            echo json_encode(array("msg" => "Password Invalid", "response" => "E"));
            exit();
        }
    } else {
        echo json_encode(array("msg" => "Account Not Found Please Register yourself.", "response" => "E"));
        exit();
    }
} else {
    echo json_encode(array("msg" => "Email Address Not Valid or Empty..!", "response" => "E"));
    exit();
}
?>
