<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');

$obj = new main();
$obj->connection();
ini_set('max_execution_time', 300);
sec_session_start();

if (login_check($mysqli) == true) {
    if (!empty($_POST["ProductName"])) {
        try {
        // Allowed sizes
        // $preset_sizes = [
        //     '500 ml | Pack of 1',
        //     '1000 ml | Pack of 1',
        //     '1000 ml | Pack of 2',
        //     '500 ml | Pack of 2'
        // ];

        $upload_url = "images/products/";
        $valid_formats = ["jpg", "jpeg", "png", "webp"];
        $PhotoPath = "";

        // Update Product
        if (!empty($_POST["ProductId"])) {
            $FieldNames = array("PhotoPath");
            $ParamArray = array();
            $ParamArray[0] = $_POST["ProductId"];
            $Fields = implode(",", $FieldNames);
            $single_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_master WHERE ProductId = ?", $FieldNames, "i", $ParamArray);
            $PhotoPath = $single_data[0]["PhotoPath"];
            
            if ($_FILES["PhotoPath"]["tmp_name"] != "") {
                $tempexp = explode('.', $_FILES["PhotoPath"]["name"]);
                $ext = end($tempexp);
                $UploadPhoto = rand(1111, 9999) . "." . $ext;
                move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto);
                $PhotoPath = $UploadPhoto;
            }

            // Handle empty fields for update
            $subCategoryIds = !empty($_POST['SubCategoryId']) ? $_POST['SubCategoryId'] : array();
            $primarySubCategoryId = !empty($subCategoryIds) ? $subCategoryIds[0] : null; // Use first as primary
            $title = $_POST['ProductName']; // Use ProductName as Title
            $description = ''; // Default empty description
            $videoURL = ''; // Default empty video URL

            // Update Product in product_master (keep primary subcategory for backward compatibility)
            $stmt = $mysqli->prepare("UPDATE product_master SET ProductName = ?, MetaTags = ?, MetaKeywords = ?, ShortDescription = ?, PhotoPath = ?, Specification = ?, ProductCode = ?, CategoryId = ?, SubCategoryId = ?, Title = ?, Description = ?, VideoURL = ? WHERE ProductId = ?");
            $stmt->bind_param(
                "ssssssssisssi",
                $_POST['ProductName'],
                $_POST['MetaTags'],
                $_POST['MetaKeywords'],
                $_POST['ShortDescription'],
                $PhotoPath,
                $_POST['Specification'],
                $_POST['ProductCode'],
                $_POST['CategoryId'],
                $primarySubCategoryId,
                $title,
                $description,
                $videoURL,
                $_POST["ProductId"]
            );
            $stmt->execute();
            $stmt->close();

            // Update product-subcategory relationships
            if (!empty($subCategoryIds)) {
                // First, delete existing relationships
                $deleteSubCatStmt = $mysqli->prepare("DELETE FROM product_subcategories WHERE ProductId = ?");
                $deleteSubCatStmt->bind_param("i", $_POST["ProductId"]);
                $deleteSubCatStmt->execute();
                $deleteSubCatStmt->close();

                // Then insert new relationships
                foreach ($subCategoryIds as $index => $subCatId) {
                    if (!empty($subCatId)) {
                        $isPrimary = ($index === 0) ? 1 : 0; // First one is primary
                        $insertSubCatStmt = $mysqli->prepare("INSERT INTO product_subcategories (ProductId, SubCategoryId, is_primary) VALUES (?, ?, ?)");
                        $insertSubCatStmt->bind_param("iii", $_POST["ProductId"], $subCatId, $isPrimary);
                        $insertSubCatStmt->execute();
                        $insertSubCatStmt->close();
                    }
                }
            }

            // Handle dynamic sizes and prices for update
            if (isset($_POST['size']) && is_array($_POST['size'])) {
                // First, delete existing price records for this product
                $deleteStmt = $mysqli->prepare("DELETE FROM product_price WHERE ProductId = ?");
                $deleteStmt->bind_param("i", $_POST["ProductId"]);
                $deleteStmt->execute();
                $deleteStmt->close();

                // Then insert new price records
                foreach ($_POST['size'] as $index => $size) {
                    $offer_price = $_POST['offer_price'][$index];
                    $mrp = $_POST['mrp'][$index];
                    $coins = $_POST['coins'][$index];

                    if (!empty($size) && isset($offer_price, $mrp, $coins)) {
                        // Generate a unique PriceId
                        $priceResult = $mysqli->query("SELECT MAX(PriceId) as max_price_id FROM product_price");
                        $priceRow = $priceResult->fetch_assoc();
                        $priceId = ($priceRow['max_price_id'] ?? 0) + 1;

                        $stmt = $mysqli->prepare("INSERT INTO product_price (PriceId, ProductId, Size, OfferPrice, MRP, Coins) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("iissss", $priceId, $_POST["ProductId"], $size, $offer_price, $mrp, $coins);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }

            // Handle Long Description in product_details table
            if (isset($_POST['LongDescription'])) {
                $longDescription = $_POST['LongDescription'];

                // Check if product_details record exists
                $checkStmt = $mysqli->prepare("SELECT Product_DetailsId FROM product_details WHERE ProductId = ?");
                $checkStmt->bind_param("i", $_POST["ProductId"]);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows > 0) {
                    // Update existing record
                    $updateDescStmt = $mysqli->prepare("UPDATE product_details SET Description = ? WHERE ProductId = ?");
                    $updateDescStmt->bind_param("si", $longDescription, $_POST["ProductId"]);
                    $updateDescStmt->execute();
                    $updateDescStmt->close();
                } else {
                    // Insert new record
                    $insertDescStmt = $mysqli->prepare("INSERT INTO product_details (ProductId, Description) VALUES (?, ?)");
                    $insertDescStmt->bind_param("is", $_POST["ProductId"], $longDescription);
                    $insertDescStmt->execute();
                    $insertDescStmt->close();
                }
                $checkStmt->close();
            }

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(["msg" => "Product updated successfully.", "response" => "S"]);
        } else {
            // Insert New Product
            if ($_FILES["PhotoPath"]["tmp_name"] != "") {
                $tempexp = explode('.', $_FILES["PhotoPath"]["name"]);
                $ext = end($tempexp);
                $UploadPhoto = rand(1111, 9999) . "." . $ext;
                move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto);
                $PhotoPath = $UploadPhoto;
            }

            // Generate a unique ProductId
            $result = $mysqli->query("SELECT MAX(ProductId) as max_id FROM product_master");
            $row = $result->fetch_assoc();
            $maxId = $row['max_id'];
            $ProductId = ($maxId === null || $maxId === '') ? 1 : (int)$maxId + 1;

            // Handle empty SubCategoryId and IsCombo
            $subCategoryIds = !empty($_POST['SubCategoryId']) ? $_POST['SubCategoryId'] : array();
            $primarySubCategoryId = !empty($subCategoryIds) ? $subCategoryIds[0] : null; // Use first as primary
            $isCombo = !empty($_POST['IsCombo']) ? $_POST['IsCombo'] : '';

            // Handle empty fields - use ProductName as Title if Title field is required
            $title = $_POST['ProductName']; // Use ProductName as Title
            $description = ''; // Default empty description
            $videoURL = ''; // Default empty video URL

            // Insert into product_master with all possible fields
            $stmt = $mysqli->prepare("INSERT INTO product_master (ProductId, ProductName, MetaTags, MetaKeywords, CategoryId, SubCategoryId, ShortDescription, Specification, ProductCode, PhotoPath, IsCombo, Title, Description, VideoURL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "issssissssssss",
                $ProductId,
                $_POST['ProductName'],
                $_POST['MetaTags'],
                $_POST['MetaKeywords'],
                $_POST['CategoryId'],
                $primarySubCategoryId,
                $_POST['ShortDescription'],
                $_POST['Specification'],
                $_POST['ProductCode'],
                $PhotoPath,
                $isCombo,
                $title,
                $description,
                $videoURL
            );
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert product: " . $stmt->error);
            }
            $stmt->close();

            // Insert product-subcategory relationships
            if (!empty($subCategoryIds)) {
                foreach ($subCategoryIds as $index => $subCatId) {
                    if (!empty($subCatId)) {
                        $isPrimary = ($index === 0) ? 1 : 0; // First one is primary
                        $insertSubCatStmt = $mysqli->prepare("INSERT INTO product_subcategories (ProductId, SubCategoryId, is_primary) VALUES (?, ?, ?)");
                        $insertSubCatStmt->bind_param("iii", $ProductId, $subCatId, $isPrimary);
                        $insertSubCatStmt->execute();
                        $insertSubCatStmt->close();
                    }
                }
            }

           // Handle dynamic sizes and prices
        if (isset($_POST['size']) && is_array($_POST['size'])) {
            foreach ($_POST['size'] as $index => $size) {
                $offer_price = $_POST['offer_price'][$index];
                $mrp = $_POST['mrp'][$index];
                $coins = $_POST['coins'][$index];

                if (!empty($size) && isset($offer_price, $mrp, $coins)) {
                    // Generate a unique PriceId
                    $priceResult = $mysqli->query("SELECT MAX(PriceId) as max_price_id FROM product_price");
                    $priceRow = $priceResult->fetch_assoc();
                    $priceId = ($priceRow['max_price_id'] ?? 0) + 1;

                    $stmt = $mysqli->prepare("INSERT INTO product_price (PriceId, ProductId, Size, OfferPrice, MRP, Coins) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iissss", $priceId, $ProductId, $size, $offer_price, $mrp, $coins);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

            // Handle Long Description in product_details table for new product
            if (isset($_POST['LongDescription']) && !empty($_POST['LongDescription'])) {
                $longDescription = $_POST['LongDescription'];
                $insertDescStmt = $mysqli->prepare("INSERT INTO product_details (ProductId, Description) VALUES (?, ?)");
                $insertDescStmt->bind_param("is", $ProductId, $longDescription);
                $insertDescStmt->execute();
                $insertDescStmt->close();
            }

            $_SESSION["QueryStatus"] = "SAVED";
            echo json_encode(["msg" => "Product saved successfully.", "response" => "S"]);
        }
        } catch (Exception $e) {
            echo json_encode(["msg" => "Database error: " . $e->getMessage(), "response" => "E"]);
        }
    } else {
        echo json_encode(["msg" => "Product name is required.", "response" => "E"]);
    }
} else {
    echo json_encode(["msg" => "Unauthorized access.", "response" => "E"]);
}
?>
