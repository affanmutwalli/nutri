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
    if (!empty($_POST["Name"]) && !empty($_POST["ProductId"]) && !empty($_POST["Review"]) && !empty($_POST["Date"])) {
        $upload_url = "images/ingredient/";
        $valid_formats = array("jpg", "jpeg", "png", "webp");
        $PhotoPath = "";
        $ext = "";

        // Handle File Upload
        if (!empty($_FILES["PhotoPath"]["name"])) {
            $name = $_FILES['PhotoPath']['name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($ext, $valid_formats)) {
                echo json_encode(array("msg" => "Select a valid file (jpg, jpeg, png, webp)", "response" => "E"));
                exit();
            }
        }

        // If Product_ReviewId is set — Update existing review
        if (!empty($_POST["Product_ReviewId"])) {
            // Fetch existing PhotoPath
            $FieldNames = array("PhotoPath");
            $ParamArray = array($_POST["Product_ReviewId"]);
            $Fields = implode(",", $FieldNames);
            $single_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_review WHERE Product_ReviewId = ?", $FieldNames, "i", $ParamArray);
            $PhotoPath = $single_data[0]["PhotoPath"];

            // Upload new image if present
            if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                $UploadPhoto = rand(11111, 99999) . "." . $ext;
                if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                    $PhotoPath = $UploadPhoto;
                }
            }

            // Convert date from DD-MM-YYYY to YYYY-MM-DD for MySQL
            $inputDate = $_POST["Date"];
            $dateFormatted = false;

            // Try DD-MM-YYYY format
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $inputDate, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];

                if (checkdate($month, $day, $year)) {
                    $dateFormatted = "$year-$month-$day";
                }
            }

            // Fallback method
            if (!$dateFormatted) {
                $dateFormatted = date("Y-m-d", strtotime(str_replace('-', '/', $inputDate)));
                if ($dateFormatted === "1970-01-01") {
                    $dateFormatted = date("Y-m-d"); // Use today's date as fallback
                }
            }

            // Update the record
            $stmt = $mysqli->prepare("UPDATE product_review SET ProductId = ?, Name = ?, Review = ?, PhotoPath = ?, Date = ? WHERE Product_ReviewId = ?");
            $stmt->bind_param("issssi", $_POST["ProductId"], $_POST["Name"], $_POST["Review"], $PhotoPath, $dateFormatted, $_POST["Product_ReviewId"]);
            $stmt->execute();
            $stmt->close();

            $_SESSION["QueryStatus"] = "UPDATED";
            echo json_encode(array("msg" => "Record updated successfully", "response" => "S"));
        } else {
            // INSERT NEW RECORD
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

            // Convert date from DD-MM-YYYY to YYYY-MM-DD for MySQL
            try {
                $inputDate = $_POST["Date"];

                // Try multiple date parsing methods
                $dateFormatted = false;

                // Method 1: Try DD-MM-YYYY format
                if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $inputDate, $matches)) {
                    $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                    $year = $matches[3];

                    // Validate the date
                    if (checkdate($month, $day, $year)) {
                        $dateFormatted = "$year-$month-$day";
                    }
                }

                // Method 2: If first method failed, try with slashes
                if (!$dateFormatted) {
                    $dateFormatted = date("Y-m-d", strtotime(str_replace('-', '/', $inputDate)));
                    if ($dateFormatted === "1970-01-01") {
                        $dateFormatted = false;
                    }
                }

                // If all methods failed
                if (!$dateFormatted) {
                    echo json_encode(array("msg" => "Invalid date format. Please use DD-MM-YYYY format (e.g., 25-12-2024).", "response" => "E"));
                    exit();
                }

                // Get the next available Product_ReviewId
                $maxIdQuery = $mysqli->query("SELECT MAX(Product_ReviewId) as max_id FROM product_review");
                $maxIdResult = $maxIdQuery->fetch_assoc();
                $reviewId = ($maxIdResult['max_id'] ?? 0) + 1;

                // Use direct mysqli insertion for better error handling
                $stmt = $mysqli->prepare("INSERT INTO product_review (Product_ReviewId, ProductId, Name, Review, Date, PhotoPath) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    echo json_encode(array("msg" => "Database prepare error: " . $mysqli->error, "response" => "E"));
                    exit();
                }

                $stmt->bind_param("iissss", $reviewId, $_POST["ProductId"], $_POST["Name"], $_POST["Review"], $dateFormatted, $PhotoPath);
                $result = $stmt->execute();

                if (!$result) {
                    echo json_encode(array("msg" => "Database execute error: " . $stmt->error, "response" => "E"));
                    exit();
                }

                $stmt->close();

                $_SESSION["QueryStatus"] = "SAVED";
                echo json_encode(array("msg" => "Record saved successfully", "response" => "S"));
            } catch (Exception $e) {
                echo json_encode(array("msg" => "Error: " . $e->getMessage(), "response" => "E"));
            }
        }
    } else {
        echo json_encode(array("msg" => "Please fill all required fields (Name, Product, Review, Date)", "response" => "E"));
    }
} else {
    echo json_encode(array("msg" => "Unauthorized access", "response" => "E"));
}
?>
