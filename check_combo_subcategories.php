<?php
include('database/dbconnection.php');

$obj = new main();
$obj->connection();

echo "<h2>Combo Products by Subcategory Analysis</h2>";

// Check which subcategories have combo products assigned
echo "<h3>1. Combo Products by Subcategory (Direct Assignment):</h3>";
$combosBySubcategory = $obj->MysqliSelect1("
    SELECT pm.SubCategoryId, sc.SubCategoryName, COUNT(*) as combo_count 
    FROM product_master pm 
    LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId 
    WHERE pm.IsCombo = 'Y' 
    GROUP BY pm.SubCategoryId, sc.SubCategoryName 
    ORDER BY combo_count DESC", 
    array("SubCategoryId", "SubCategoryName", "combo_count"), "", array());

if (!empty($combosBySubcategory)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th><th>Combo Count</th></tr>";
    foreach ($combosBySubcategory as $combo) {
        echo "<tr><td>{$combo['SubCategoryId']}</td><td>{$combo['SubCategoryName']}</td><td>{$combo['combo_count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No combo products found with subcategory assignments.</p>";
}

// Check individual combo products and their subcategories
echo "<h3>2. Individual Combo Products and Their Subcategories:</h3>";
$comboProducts = $obj->MysqliSelect1("
    SELECT pm.ProductId, pm.ProductName, pm.SubCategoryId, sc.SubCategoryName
    FROM product_master pm 
    LEFT JOIN sub_category sc ON pm.SubCategoryId = sc.SubCategoryId 
    WHERE pm.IsCombo = 'Y' 
    ORDER BY pm.SubCategoryId, pm.ProductId", 
    array("ProductId", "ProductName", "SubCategoryId", "SubCategoryName"), "", array());

if (!empty($comboProducts)) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ProductId</th><th>ProductName</th><th>SubCategoryId</th><th>SubCategoryName</th></tr>";
    foreach ($comboProducts as $product) {
        echo "<tr>";
        echo "<td>{$product['ProductId']}</td>";
        echo "<td>" . substr($product['ProductName'], 0, 50) . "...</td>";
        echo "<td>{$product['SubCategoryId']}</td>";
        echo "<td>{$product['SubCategoryName']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No combo products found.</p>";
}

// Check if multiple subcategories system is being used
echo "<h3>3. Multiple Subcategories System Check:</h3>";
$multiSubQuery = "SELECT COUNT(*) as count FROM product_subcategories ps
                  INNER JOIN product_master pm ON ps.ProductId = pm.ProductId
                  WHERE pm.IsCombo = 'Y'";
$multiSubResult = $obj->MysqliSelect1($multiSubQuery, array("count"), "", array());
$multiSubCount = $multiSubResult[0]['count'] ?? 0;

if ($multiSubCount > 0) {
    echo "<p><strong>Multiple subcategories system is active:</strong> {$multiSubCount} combo product-subcategory relationships found.</p>";
    
    // Get subcategories from the junction table
    echo "<h4>Subcategories from Multiple System:</h4>";
    $junctionQuery = "
        SELECT sc.SubCategoryId, sc.SubCategoryName, COUNT(DISTINCT pm.ProductId) as combo_count
        FROM product_subcategories ps
        INNER JOIN product_master pm ON ps.ProductId = pm.ProductId
        INNER JOIN sub_category sc ON ps.SubCategoryId = sc.SubCategoryId
        WHERE pm.IsCombo = 'Y'
        GROUP BY sc.SubCategoryId, sc.SubCategoryName
        ORDER BY combo_count DESC";
    
    $junctionSubs = $obj->MysqliSelect1($junctionQuery, 
        array("SubCategoryId", "SubCategoryName", "combo_count"), "", array());
    
    if (!empty($junctionSubs)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th><th>Combo Count</th></tr>";
        foreach ($junctionSubs as $jsub) {
            echo "<tr><td>{$jsub['SubCategoryId']}</td><td>{$jsub['SubCategoryName']}</td><td>{$jsub['combo_count']}</td></tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p>Multiple subcategories system is not being used for combo products.</p>";
}

// Show all subcategories and which ones are missing combo products
echo "<h3>4. All Subcategories and Missing Combo Assignments:</h3>";
$allSubcategories = $obj->MysqliSelect1("SELECT SubCategoryId, SubCategoryName FROM sub_category ORDER BY SubCategoryId", 
    array("SubCategoryId", "SubCategoryName"), "", array());

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>SubCategoryId</th><th>SubCategoryName</th><th>Has Combo Products?</th><th>Action Needed</th></tr>";

foreach ($allSubcategories as $subcat) {
    $hasComboQuery = "SELECT COUNT(*) as count FROM product_master WHERE IsCombo = 'Y' AND SubCategoryId = ?";
    $hasComboResult = $obj->MysqliSelect1($hasComboQuery, array("count"), "i", array($subcat['SubCategoryId']));
    $hasCombo = ($hasComboResult[0]['count'] ?? 0) > 0;
    
    echo "<tr>";
    echo "<td>{$subcat['SubCategoryId']}</td>";
    echo "<td>{$subcat['SubCategoryName']}</td>";
    echo "<td>" . ($hasCombo ? "✅ YES" : "❌ NO") . "</td>";
    echo "<td>" . ($hasCombo ? "None" : "⚠️ Need to assign combo products") . "</td>";
    echo "</tr>";
}
echo "</table>";

?>
