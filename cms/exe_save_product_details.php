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

if (login_check($mysqli) == true) {
    if (!empty($_POST["Product_DetailsId"]) || !empty($_POST["ProductId"])) {
        $upload_url = "images/products/";
        $valid_formats = array("jpg", "jpeg", "png", "webp");
        $PhotoPath = "";
        $ImagePath = "";

        // Handle file uploads
        if (!empty($_FILES["PhotoPath"]["name"])) {
            $photoExt = strtolower(pathinfo($_FILES["PhotoPath"]["name"], PATHINFO_EXTENSION));
            if (!in_array($photoExt, $valid_formats)) {
                echo json_encode(["msg" => "Invalid PhotoPath file (jpg, jpeg, png, webp only)", "response" => "E"]);
                exit();
            }
        }

        if (!empty($_FILES["ImagePath"]["name"])) {
            $imageExt = strtolower(pathinfo($_FILES["ImagePath"]["name"], PATHINFO_EXTENSION));
            if (!in_array($imageExt, $valid_formats)) {
                echo json_encode(["msg" => "Invalid ImagePath file (jpg, jpeg, png, webp only)", "response" => "E"]);
                exit();
            }
        }

        // Create upload directory if not exists
        if (!file_exists($upload_url)) {
            mkdir($upload_url, 0777, true);
        }

        // Update Record
        if (!empty($_POST["Product_DetailsId"])) {
            $Product_DetailsId = intval($_POST["Product_DetailsId"]);

            // Fetch existing data
            $existing = $obj->MysqliSelect1("SELECT PhotoPath, ImagePath FROM product_details WHERE Product_DetailsId = ?", ["Product_DetailsId"], "i", [$Product_DetailsId]);
            $PhotoPath = $existing[0]["PhotoPath"] ?? "";
            $ImagePath = $existing[0]["ImagePath"] ?? "";

            // Upload PhotoPath
            if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                $UploadPhoto = rand(11111, 99999) . "." . $photoExt;
                if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                    $PhotoPath = $UploadPhoto;
                } else {
                    echo json_encode(["msg" => "Photo upload failed. Check folder permissions.", "response" => "E"]);
                    exit();
                }
            }

            // Upload ImagePath
            if (!empty($_FILES["ImagePath"]["tmp_name"])) {
                $UploadImage = rand(11111, 99999) . "." . $imageExt;
                if (move_uploaded_file($_FILES["ImagePath"]["tmp_name"], $upload_url . $UploadImage)) {
                    $ImagePath = $UploadImage;
                } else {
                    echo json_encode(["msg" => "Image upload failed. Check folder permissions.", "response" => "E"]);
                    exit();
                }
            }

            // Update Query
            $stmt = $mysqli->prepare("UPDATE product_details SET ProductId = ?, Description = ?, PhotoPath = ?, ImagePath = ? WHERE Product_DetailsId = ?");
            $stmt->bind_param("isssi", $_POST["ProductId"], $_POST["Description"], $PhotoPath, $ImagePath, $Product_DetailsId);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(["msg" => "Record updated successfully", "response" => "S"]);

        } else {
            // Upload PhotoPath
            if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                $UploadPhoto = rand(11111, 99999) . "." . $photoExt;
                if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                    $PhotoPath = $UploadPhoto;
                } else {
                    echo json_encode(["msg" => "Photo upload failed. Check folder permissions.", "response" => "E"]);
                    exit();
                }
            }

            // Upload ImagePath
            if (!empty($_FILES["ImagePath"]["tmp_name"])) {
                $UploadImage = rand(11111, 99999) . "." . $imageExt;
                if (move_uploaded_file($_FILES["ImagePath"]["tmp_name"], $upload_url . $UploadImage)) {
                    $ImagePath = $UploadImage;
                } else {
                    echo json_encode(["msg" => "Image upload failed. Check folder permissions.", "response" => "E"]);
                    exit();
                }
            }

            // Insert Query
            $ParamArray = [$_POST["ProductId"], $_POST["Description"], $PhotoPath, $ImagePath];
            $obj->fInsertNew("INSERT INTO product_details (ProductId, Description, PhotoPath, ImagePath) VALUES (?, ?, ?, ?)", "isss", $ParamArray);

            $_SESSION["QueryStatus"] = "SAVED";
            echo json_encode(["msg" => "Record saved successfully", "response" => "S"]);
        }
    }
}
?>
