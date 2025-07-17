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
    if (!empty($_POST["IngredientName"])) {

        $upload_url = "images/products/";
        $valid_formats = ["jpg", "jpeg", "png", "webp"];
        $IngredientImage = "";

        // If IngredientId exists, update
        if (!empty($_POST["IngredientId"])) {
            $IngredientId = $_POST["IngredientId"];

            // Get existing image
            $FieldNames = array("IngredientImage");
            $ParamArray = array($IngredientId);
            $single_data = $obj->MysqliSelect1("SELECT IngredientImage FROM product_ingredients WHERE IngredientId = ?", $FieldNames, "i", $ParamArray);
            $IngredientImage = $single_data[0]["IngredientImage"];

            // Upload new image if provided
            if (!empty($_FILES["IngredientImage"]["tmp_name"])) {
                $tempexp = explode('.', $_FILES["IngredientImage"]["name"]);
                $ext = strtolower(end($tempexp));

                if (in_array($ext, $valid_formats)) {
                    $UploadPhoto = rand(1111, 9999) . "." . $ext;
                    move_uploaded_file($_FILES["IngredientImage"]["tmp_name"], $upload_url . $UploadPhoto);
                    $IngredientImage = $UploadPhoto;
                }
            }

            // Update query
            $stmt = $mysqli->prepare("UPDATE product_ingredients SET ProductId = ?, IngredientName = ?, IngredientDescription = ?, IngredientImage = ?, SortOrder = ? WHERE IngredientId = ?");
            $stmt->bind_param(
                "isssii",
                $_POST['ProductId'],
                $_POST['IngredientName'],
                $_POST['IngredientDescription'],
                $IngredientImage,
                $_POST['SortOrder'],
                $IngredientId
            );
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(["msg" => "Ingredient updated successfully.", "response" => "S"]);

        } else {
            // Insert new Ingredient
            if (!empty($_FILES["IngredientImage"]["tmp_name"])) {
                $tempexp = explode('.', $_FILES["IngredientImage"]["name"]);
                $ext = strtolower(end($tempexp));

                if (in_array($ext, $valid_formats)) {
                    $UploadPhoto = rand(1111, 9999) . "." . $ext;
                    move_uploaded_file($_FILES["IngredientImage"]["tmp_name"], $upload_url . $UploadPhoto);
                    $IngredientImage = $UploadPhoto;
                }
            }

            $stmt = $mysqli->prepare("INSERT INTO product_ingredients (ProductId, IngredientName, IngredientDescription, IngredientImage, SortOrder) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "isssi",
                $_POST['ProductId'],
                $_POST['IngredientName'],
                $_POST['IngredientDescription'],
                $IngredientImage,
                $_POST['SortOrder']
            );
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "SAVED";
            echo json_encode(["msg" => "Ingredient saved successfully.", "response" => "S"]);
        }
    } else {
        echo json_encode(["msg" => "Ingredient name is required.", "response" => "E"]);
    }
} else {
    echo json_encode(["msg" => "Unauthorized access.", "response" => "E"]);
}
?>
