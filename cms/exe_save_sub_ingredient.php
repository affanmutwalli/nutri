<?php ob_start(); ?>
<?php
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
        $upload_url = "images/ingredient/";
        $valid_formats = array("jpg", "jpeg", "png", "webp");
        $PhotoPath = "";

        // Handle File Upload
        if (!empty($_FILES["PhotoPath"]["name"])) {
            $name = $_FILES['PhotoPath']['name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($ext, $valid_formats)) {
                echo json_encode(array("msg" => "Select a valid file (jpg, jpeg, png, webp)", "response" => "E"));
                exit();
            }
        }

        // If IngredientId is set — Update
        if (!empty($_POST["IngredientId"])) {
            // Fetch existing PhotoPath
            $FieldNames = array("PhotoPath");
            $ParamArray = array($_POST["IngredientId"]);
            $Fields = implode(",", $FieldNames);
            $single_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_ingredients WHERE IngredientId = ?", $FieldNames, "i", $ParamArray);
            $PhotoPath = $single_data[0]["PhotoPath"];

            // Upload new image if present
            if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                $UploadPhoto = rand(11111, 99999) . "." . $ext;
                if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                    $PhotoPath = $UploadPhoto;
                }
            }

            // Update Query
            $stmt = $mysqli->prepare("UPDATE product_ingredients SET ProductId = ?, IngredientName = ?, PhotoPath = ? WHERE IngredientId = ?");
            $stmt->bind_param("issi", $_POST["ProductId"], $_POST["IngredientName"], $PhotoPath, $_POST["IngredientId"]);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(array("msg" => "Record updated successfully", "response" => "S"));
        } else {

if (!file_exists($upload_url)) {
    mkdir($upload_url, 0777, true);
}

// Upload new image if present
if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
    $UploadPhoto = rand(11111, 99999) . "." . $ext;
    if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
        $PhotoPath = $UploadPhoto;
    } else {
        echo json_encode(array("msg" => "Image upload failed. Check folder permissions.", "response" => "E"));
        exit();
    }
}
            // Insert New Ingredient
            // Ensure upload directory exists

            $ParamArray = array($_POST["ProductId"], $_POST["IngredientName"], $PhotoPath);
            $obj->fInsertNew("INSERT INTO product_ingredients (ProductId, IngredientName, PhotoPath) VALUES (?, ?, ?)", "iss", $ParamArray);

            $_SESSION["QueryStatus"] = "SAVED";
            echo json_encode(array("msg" => "Record saved successfully", "response" => "S"));
        }
    }
}
?>