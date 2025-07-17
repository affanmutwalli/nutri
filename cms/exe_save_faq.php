<?php 
ob_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();
ini_set('max_execution_time', 300);
sec_session_start();

header("Content-Type: application/json");

if (login_check($mysqli) == true) {

    if (!empty($_POST["ProductId"]) && !empty($_POST["Question"])) {

        $ProductId = $_POST['ProductId'];
        $Question = $_POST['Question'];
        $Answer = $_POST['Answer'];

        // If Editing Existing FAQ
        if (!empty($_POST["FAQId"])) {

            $FAQId = $_POST["FAQId"];

            // Update Record
            $stmt = $mysqli->prepare("UPDATE faqs SET ProductId = ?, Question = ?, Answer = ? WHERE FAQId = ?");
            $stmt->bind_param("issi", $ProductId, $Question, $Answer, $FAQId);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(["msg" => "Product FAQ updated successfully.", "response" => "S"]);

        } else {
            // Insert Mode
            $stmt = $mysqli->prepare("INSERT INTO faqs (ProductId, Question, Answer) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $ProductId, $Question, $Answer);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "SAVED";
            echo json_encode(["msg" => "Product FAQ saved successfully.", "response" => "S"]);
        }

    } else {
        echo json_encode(["msg" => "Product ID and Question are required.", "response" => "E"]);
    }

} else {
    echo json_encode(["msg" => "Unauthorized access.", "response" => "E"]);
}
?>
