<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Fix Combo Product Subcategory Assignments</h2>";

// Define the correct subcategory assignments based on product names
$subcategoryAssignments = array(
    // Diabetic Wellness (ID: 4)
    4 => array(
        'name' => 'Diabetic Wellness',
        'keywords' => array('Diabetic Care', 'Karela', 'Neem', 'Jamun')
    ),
    // Blood Purifier (ID: 6) 
    6 => array(
        'name' => 'Blood Purifier',
        'keywords' => array('BP Care', 'Blood')
    ),
    // Pain Reliever (ID: 3) - we'll check if any products should go here
    3 => array(
        'name' => 'Pain Reliever',
        'keywords' => array('Pain', 'Relief', 'Joint')
    )
);

// Get all combo products
$comboProducts = $obj->MysqliSelect1("
    SELECT ProductId, ProductName, SubCategoryId
    FROM product_master 
    WHERE IsCombo = 'Y' 
    ORDER BY ProductId", 
    array("ProductId", "ProductName", "SubCategoryId"), "", array());

echo "<h3>Current Combo Products and Suggested Reassignments:</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>ProductId</th><th>ProductName</th><th>Current SubCat</th><th>Suggested SubCat</th><th>Reason</th></tr>";

$reassignments = array();

foreach ($comboProducts as $product) {
    $productName = $product['ProductName'];
    $currentSubCat = $product['SubCategoryId'];
    $suggestedSubCat = $currentSubCat;
    $reason = "No change needed";
    
    // Check if product should be reassigned based on keywords
    foreach ($subcategoryAssignments as $subCatId => $subCatInfo) {
        foreach ($subCatInfo['keywords'] as $keyword) {
            if (stripos($productName, $keyword) !== false) {
                if ($currentSubCat != $subCatId) {
                    $suggestedSubCat = $subCatId;
                    $reason = "Contains '{$keyword}' - should be in {$subCatInfo['name']}";
                    $reassignments[] = array(
                        'ProductId' => $product['ProductId'],
                        'ProductName' => $productName,
                        'CurrentSubCat' => $currentSubCat,
                        'NewSubCat' => $subCatId,
                        'Reason' => $reason
                    );
                }
                break 2; // Exit both loops once a match is found
            }
        }
    }
    
    $rowColor = ($suggestedSubCat != $currentSubCat) ? "style='background-color: #ffffcc;'" : "";
    echo "<tr {$rowColor}>";
    echo "<td>{$product['ProductId']}</td>";
    echo "<td>" . substr($productName, 0, 60) . "...</td>";
    echo "<td>{$currentSubCat}</td>";
    echo "<td>{$suggestedSubCat}</td>";
    echo "<td>{$reason}</td>";
    echo "</tr>";
}

echo "</table>";

if (!empty($reassignments)) {
    echo "<h3>Proposed Changes Summary:</h3>";
    echo "<ul>";
    foreach ($reassignments as $change) {
        echo "<li><strong>Product {$change['ProductId']}:</strong> Move from SubCategory {$change['CurrentSubCat']} to {$change['NewSubCat']} - {$change['Reason']}</li>";
    }
    echo "</ul>";
    
    echo "<h3>Execute Changes:</h3>";
    echo "<p><strong>Click the button below to apply these changes:</strong></p>";
    echo "<form method='POST' style='margin: 20px 0;'>";
    echo "<input type='hidden' name='execute_changes' value='1'>";
    echo "<button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 16px;'>Apply Subcategory Changes</button>";
    echo "</form>";
} else {
    echo "<p><strong>No changes needed - all products are correctly assigned.</strong></p>";
}

// Execute changes if requested
if (isset($_POST['execute_changes']) && $_POST['execute_changes'] == '1') {
    echo "<h3>Executing Changes...</h3>";
    
    $changesApplied = 0;
    $errors = array();
    
    foreach ($reassignments as $change) {
        try {
            // Update the product's subcategory
            $updateQuery = "UPDATE product_master SET SubCategoryId = ? WHERE ProductId = ?";
            $result = $obj->fUpdateNew($updateQuery, "ii", array($change['NewSubCat'], $change['ProductId']));

            echo "<p>✅ Updated Product {$change['ProductId']}: {$change['Reason']}</p>";
            $changesApplied++;

            // Also add to the multiple subcategories table if it doesn't exist
            $checkJunctionQuery = "SELECT COUNT(*) as count FROM product_subcategories WHERE ProductId = ? AND SubCategoryId = ?";
            $checkResult = $obj->MysqliSelect1($checkJunctionQuery, array("count"), "ii", array($change['ProductId'], $change['NewSubCat']));

            if (($checkResult[0]['count'] ?? 0) == 0) {
                $insertJunctionQuery = "INSERT INTO product_subcategories (ProductId, SubCategoryId, is_primary) VALUES (?, ?, ?)";
                $obj->fInsertNew($insertJunctionQuery, "iii", array($change['ProductId'], $change['NewSubCat'], 1));
                echo "<p>   ➕ Added to multiple subcategories table</p>";
            }
        } catch (Exception $e) {
            $errors[] = "Error updating Product {$change['ProductId']}: " . $e->getMessage();
        }
    }
    
    echo "<h4>Summary:</h4>";
    echo "<p><strong>Changes Applied:</strong> {$changesApplied}</p>";
    
    if (!empty($errors)) {
        echo "<p><strong>Errors:</strong></p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li style='color: red;'>{$error}</li>";
        }
        echo "</ul>";
    }
    
    if ($changesApplied > 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Changes applied successfully! The combo filtering should now show the missing subcategories.</p>";
        echo "<p><a href='combos.php' target='_blank'>🔗 Test the updated combos page</a></p>";
    }
}

?>
