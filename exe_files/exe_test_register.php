<?php
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

// Start the session
sec_session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['name']) && !empty($_POST['name'])
    && isset($_POST['email']) && !empty($_POST['email'])
    && isset($_POST['mobile_number']) && !empty($_POST['mobile_number'])
    && isset($_POST['password']) && !empty($_POST['password'])) {

    // Sanitize input data to prevent SQL Injection
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mobile_number = filter_var($_POST['mobile_number'], FILTER_SANITIZE_NUMBER_INT);
    $password = $_POST['password'];
    $pass = password_hash($password, PASSWORD_DEFAULT);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["msg" => "Invalid email format.", "response" => "E"]);
        exit;
    }

    // Validate mobile number (simple check)
    if (strlen($mobile_number) < 10) {
        echo json_encode(["msg" => "Invalid mobile number.", "response" => "E"]);
        exit;
    }

    $datetime = new DateTime();
    $datetime->modify('+12 hours');
    $CreatedAt = $datetime->format('j M Y h:i A');
    $IsActive = "Y";

    // Prepare for querying the database (with proper field names and parameters)
    $FieldNames = array("MobileNo", "Email");
    $ParamArray = array($email, $mobile_number); // Param for Email and Mobile Number

    // SQL query to fetch customer data
    $Fields = implode(",", $FieldNames);
    $customer_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM customer_master WHERE Email = ? AND MobileNo = ?", $FieldNames, "ss", $ParamArray);

    if (empty($customer_data)) {
        $OTP = rand(100000, 999999); // Generate OTP

        // Proceed with insertion if customer data doesn't exist
        $ParamArray = array($name, $email, $mobile_number, $pass, $OTP, $CreatedAt, $IsActive); 
        $InputDocId = $obj->fInsertNew("INSERT INTO customer_master (Name, Email, MobileNo, Pass, VCode, CreationDate, IsActive) VALUES (?, ?, ?, ?, ?, ?, ?)", "sssssss", $ParamArray);

        if ($InputDocId) {
            // Fetch the newly registered customer data
            $register_customer = $obj->MysqliSelect1("SELECT " . $Fields . " FROM customer_master WHERE MobileNo = ? AND Email = ?", $FieldNames, "ss", $ParamArray);

            // Set session variables
            $_SESSION["MobileNo"] = $register_customer[0]["MobileNo"];
            $_SESSION["Email"] = $register_customer[0]["Email"];
            
            // Store the verification code in the session for later use
            $_SESSION['OTP'] = $OTP;

            // Return success response
            echo json_encode(["msg" => "Registration Successful. Verification code sent.", "response" => "S"]);
        }
    } else {
        // Respond with error if email or mobile already exists
        echo json_encode(["msg" => "Mobile Number Or Email Already Exist.", "response" => "E"]);
    }
} else {
    // Respond if required fields are missing
    echo json_encode(["msg" => "Please provide all required fields.", "response" => "E"]);
}
?>
