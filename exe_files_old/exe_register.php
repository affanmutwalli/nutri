<?php
session_start();
ob_start();
include_once '../cms/includes/db_connect.php';
include_once '../cms/includes/functions.php';
include('../cms/includes/urls.php');
include('../cms/database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

// Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['name']) && !empty($_POST['name'])
    && isset($_POST['email']) && !empty($_POST['email'])
    && isset($_POST['mobile_no']) && !empty($_POST['mobile_no'])
    && isset($_POST['password']) && !empty($_POST['password'])) {

    // Sanitize input data to prevent SQL Injection
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars($_POST['email']);
    $mobile_no = htmlspecialchars($_POST['mobile_no']);
    $password = $_POST['password'];
    $pass = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);



    // Validate mobile number (simple check)
    if (strlen($mobile_no) < 10) {
        echo json_encode(["msg" => "Invalid mobile number.", "response" => "E"]);
        exit;
    }
    
    $CreatedAt = date('j M Y H:i'); // Ensures the format includes 12-hour format with AM/PM
    $IsActive = "N";
    
    
    // Prepare for querying the database (with proper field names and parameters)
    $FieldNames = array("MobileNo", "Email");
    $ParamArray = array($email, $mobile_no); // Param for Email and Mobile Number

    // SQL query to fetch customer data
    $Fields = implode(",", $FieldNames);
    $customer_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM customer_master WHERE Email = ? AND MobileNo = ?", $FieldNames, "ss", $ParamArray);
    
    if (empty($customer_data)) {
        $OTP = rand(100000, 999999); // Generate OTP
        // Proceed with insertion if customer data doesn't exist
        $ParamArray = array($name, $email, $mobile_no, $pass, $OTP, $CreatedAt, $IsActive); 
        $InputDocId = $obj->fInsertNew("INSERT INTO customer_master (Name, Email, MobileNo, Pass, OTP, CreationDate, IsActive) VALUES (?, ?, ?, ?, ?, ?, ?)", "sssssss", $ParamArray);
        
        if ($InputDocId) {
            // Fetch the newly registered customer data
            $FieldNames = array("MobileNo", "Email");
            $ParamArray = array($mobile_no,$email,); // Param for Email and Mobile Number
            // SQL query to fetch customer data
            $Fields = implode(",", $FieldNames);
            $register_customer = $obj->MysqliSelect1("SELECT " . $Fields . " FROM customer_master WHERE MobileNo = ? AND Email = ?", $FieldNames, "ss", $ParamArray);
            // Set session variables
            $Date = date('j M Y');
            
            $subject = "My Nutrify - Your OTP Code for Verification";
            $message = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8' />
                <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                <meta http-equiv='X-UA-Compatible' content='ie=edge' />
                <title>Email Verification</title>
                <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap' rel='stylesheet' />
                <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' rel='stylesheet'>
            </head>
            <body style='margin: 0; font-family: \"Poppins\", sans-serif; background: #ffffff; font-size: 14px;'>
                <div style='max-width: 680px; margin: 0 auto; padding: 45px 30px 60px; background: #c1ff72; font-size: 14px; color: #434343;'>
                    <header>
                        <table style='width: 100%;'>
                            <tbody>
                                <tr style='height: 0;'>
                                    <td><img alt='' src='https://shrishivshankarjewellers.com/exe_files/logo.png' width='60%' style='height: auto;' /></td>
                                    <td style='text-align: right;'><span style='font-size: 16px; color: #000; white-space: nowrap;'>$Date</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </header>

                    <main>
                        <div style='margin: 0; margin-top: 70px; padding: 92px 30px 115px; background: #ffffff; border-radius: 30px; text-align: center;'>
                            <div style='width: 100%; max-width: 489px; margin: 0 auto;'>
                                <h1 style='margin: 0; font-size: 24px; font-weight: 500; color: #1f1f1f;'>Your OTP For My Nutrify</h1>
                                <p style='margin: 0; margin-top: 17px; font-size: 16px; font-weight: 500;'>Hey <b> $name,</b></p>
                                <p style='margin: 0; margin-top: 17px; font-weight: 500;'>Thank you for choosing My Nutrify. Use the following OTP to complete the procedure to verify your account. OTP is valid for <span style='font-weight: 600; color: #1f1f1f;'>5 minutes</span>. Do not share this code with others.</p>
                                <p style='margin: 0; margin-top: 60px; font-size: 40px; font-weight: 600; color: #ec6504;'>$OTP</p>
                            </div>
                        </div>
                        <div style='text-align: center; margin-top: 20px;'>
                            <p>My Nutrify</p>
                            <div style='margin-top: 10px;'>
                                <a href='https://www.facebook.com/p/My-nutrify-100086009867166/' target='_blank' style='margin: 0 15px; text-decoration: none; color: #4267B2;'>
                                    <i class='fab fa-facebook fa-2x'></i>
                                </a>
                                <a href='https://www.instagram.com/my_nutrify/' target='_blank' style='margin: 0 15px; text-decoration: none; color: #C13584;'>
                                    <i class='fab fa-instagram fa-2x'></i>
                                </a>
                                <a href='https://wa.me/8329566751' target='_blank' style='margin: 0 15px; text-decoration: none; color: #25D366;'>
                                    <i class='fab fa-whatsapp fa-2x'></i>
                                </a>
                            </div>
                        </div>
                    </main>
                </div>
            </body>
            </html>";

            // Set the email headers to send HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: verification@mynutrify.com" . "\r\n";

            // Send the email
            if (mail($email, $subject, $message, $headers)) {
                 $_SESSION["MobileNo"] = $register_customer[0]["MobileNo"];
                 $_SESSION["Email"] = $register_customer[0]["Email"];
                            echo json_encode(["msg" => "OTP Sent to Email Successfully.", "response" => "S"]);
            } else {
                echo json_encode(["msg" => "Error: Could not send verification email.", "response" => "E"]);
            }
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
