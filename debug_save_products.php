<?php
// Debug Save Products - See what data is being received
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once 'cms/includes/db_connect.php';
include_once 'cms/includes/functions.php';
include('cms/includes/urls.php');
include('cms/database/dbconnection.php');

$obj = new main();
$obj->connection();
sec_session_start();

echo "<h1>🔍 Debug Save Products - Data Received</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .debug-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ddd; }
    .error-box { background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";

if (login_check($mysqli) == true) {
    echo "<div class='debug-box'>";
    echo "<h3>📋 POST Data Received</h3>";
    echo "<pre>" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
    echo "</div>";

    echo "<div class='debug-box'>";
    echo "<h3>📋 FILES Data Received</h3>";
    echo "<pre>" . htmlspecialchars(print_r($_FILES, true)) . "</pre>";
    echo "</div>";

    if (!empty($_POST["ProductName"])) {
        echo "<div class='success-box'>";
        echo "<h3>✅ Product Form Submitted</h3>";
        echo "<p><strong>Product Name:</strong> " . htmlspecialchars($_POST["ProductName"]) . "</p>";
        
        if (isset($_POST['SubCategoryId'])) {
            echo "<p><strong>SubCategoryId Type:</strong> " . gettype($_POST['SubCategoryId']) . "</p>";
            echo "<p><strong>SubCategoryId Value:</strong></p>";
            echo "<pre>" . htmlspecialchars(print_r($_POST['SubCategoryId'], true)) . "</pre>";
            
            if (is_array($_POST['SubCategoryId'])) {
                echo "<p><strong>✅ Received as array with " . count($_POST['SubCategoryId']) . " items</strong></p>";
                foreach ($_POST['SubCategoryId'] as $index => $subCatId) {
                    echo "<p>Index $index: $subCatId</p>";
                }
            } else {
                echo "<p><strong>❌ Received as single value, not array</strong></p>";
            }
        } else {
            echo "<p><strong>❌ No SubCategoryId received</strong></p>";
        }
        echo "</div>";

        // Test the save logic
        echo "<div class='debug-box'>";
        echo "<h3>🧪 Testing Save Logic</h3>";
        
        $subCategoryIds = !empty($_POST['SubCategoryId']) ? $_POST['SubCategoryId'] : array();
        echo "<p><strong>Processed subCategoryIds:</strong></p>";
        echo "<pre>" . htmlspecialchars(print_r($subCategoryIds, true)) . "</pre>";
        
        if (!empty($subCategoryIds)) {
            echo "<p><strong>✅ Will process " . count($subCategoryIds) . " sub-categories</strong></p>";
            foreach ($subCategoryIds as $index => $subCatId) {
                $isPrimary = ($index === 0) ? 'Yes' : 'No';
                echo "<p>SubCategory $subCatId - Primary: $isPrimary</p>";
            }
        } else {
            echo "<p><strong>❌ No sub-categories to process</strong></p>";
        }
        echo "</div>";

        // Check if this is an update or insert
        if (!empty($_POST["ProductId"])) {
            echo "<div class='debug-box'>";
            echo "<h3>🔄 UPDATE Mode</h3>";
            echo "<p><strong>Product ID:</strong> " . htmlspecialchars($_POST["ProductId"]) . "</p>";
            
            // Show current assignments
            $currentQuery = "SELECT ps.SubCategoryId, sc.SubCategoryName, ps.is_primary
                           FROM product_subcategories ps
                           JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
                           WHERE ps.ProductId = ?";
            $stmt = mysqli_prepare($mysqli, $currentQuery);
            mysqli_stmt_bind_param($stmt, "i", $_POST["ProductId"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                echo "<p><strong>Current Assignments:</strong></p>";
                echo "<ul>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $primary = $row['is_primary'] ? ' (Primary)' : '';
                    echo "<li>{$row['SubCategoryName']} (ID: {$row['SubCategoryId']})$primary</li>";
                }
                echo "</ul>";
            } else {
                echo "<p><strong>No current assignments found</strong></p>";
            }
            mysqli_stmt_close($stmt);
            echo "</div>";
        } else {
            echo "<div class='debug-box'>";
            echo "<h3>➕ INSERT Mode</h3>";
            echo "<p>This will be a new product</p>";
            echo "</div>";
        }

    } else {
        echo "<div class='error-box'>";
        echo "<h3>❌ No Product Data</h3>";
        echo "<p>No ProductName received. Form might not be submitted correctly.</p>";
        echo "</div>";
    }

    echo "<div class='debug-box'>";
    echo "<h3>📋 Instructions</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='cms/products.php' target='_blank'>CMS Products</a></li>";
    echo "<li>Edit any product</li>";
    echo "<li>Select multiple sub-categories</li>";
    echo "<li>Change the form action to point to this debug file temporarily</li>";
    echo "<li>Submit the form to see what data is being sent</li>";
    echo "</ol>";
    echo "</div>";

} else {
    echo "<div class='error-box'>";
    echo "<h3>❌ Not Logged In</h3>";
    echo "<p>Please log in to the CMS first.</p>";
    echo "</div>";
}

echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>
