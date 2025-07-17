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

    if (!empty($_POST["ProductId"]) && !empty($_POST["Title"])) {

        $upload_url = "images/ingredient/";
        $valid_formats = ["jpg", "jpeg", "png", "webp"];
        $PhotoPath = "";

        // If Editing Existing Product Benefit
        if (!empty($_POST["Product_BenefitId"])) {

            // Fetch Existing Photo
            $FieldNames = array("PhotoPath");
            $ParamArray = array($_POST["Product_BenefitId"]);
            $Fields = implode(",", $FieldNames);
            $single_data = $obj->MysqliSelect1("SELECT $Fields FROM product_benefits WHERE Product_BenefitId = ?", $FieldNames, "i", $ParamArray);

            $PhotoPath = $single_data[0]["PhotoPath"];

            // Handle New Photo Upload
            if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                $tempexp = explode('.', $_FILES["PhotoPath"]["name"]);
                $ext = strtolower(end($tempexp));

                if (in_array($ext, $valid_formats)) {
                    $UploadPhoto = rand(1111, 9999) . "_" . time() . "." . $ext;
                    if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                        $PhotoPath = $UploadPhoto;
                    }
                }
            }

            // Update Record
            $stmt = $mysqli->prepare("UPDATE product_benefits SET ProductId = ?, Title = ?, ShortDescription = ?, PhotoPath = ? WHERE Product_BenefitId = ?");
            $stmt->bind_param("isssi", $_POST['ProductId'], $_POST['Title'], $_POST['ShortDescription'], $PhotoPath, $_POST['Product_BenefitId']);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(["msg" => "Product benefit updated successfully.", "response" => "S"]);

        } else {
            // Insert Mode: Handle Photo Upload
            if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                $tempexp = explode('.', $_FILES["PhotoPath"]["name"]);
                $ext = strtolower(end($tempexp));

                if (in_array($ext, $valid_formats)) {
                    $UploadPhoto = rand(1111, 9999) . "_" . time() . "." . $ext;
                    if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                        $PhotoPath = $UploadPhoto;
                    }
                }
            }

            // Insert Record
            $stmt = $mysqli->prepare("INSERT INTO product_benefits (ProductId, Title, ShortDescription, PhotoPath) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $_POST['ProductId'], $_POST['Title'], $_POST['ShortDescription'], $PhotoPath);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "SAVED";
            echo json_encode(["msg" => "Product benefit saved successfully.", "response" => "S"]);
        }

    } else {
        echo json_encode(["msg" => "Product title and ID are required.", "response" => "E"]);
    }

} else {
    echo json_encode(["msg" => "Unauthorized access.", "response" => "E"]);
}
?>
