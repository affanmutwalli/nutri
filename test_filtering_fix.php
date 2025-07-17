<?php
// Test Filtering Fix
include_once 'database/dbconnection.php';

$obj = new main();
$mysqli = $obj->connection();

echo "<h1>🧪 Test Filtering Fix</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success-box { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
    .test-box { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; }
    button { background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
    button:hover { background: #0056b3; }
    .filter-result { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #ddd; }
</style>";

echo "<div class='success-box'>";
echo "<h3>✅ Filtering Fix Applied</h3>";
echo "<p>Updated <code>exe_files/fetch_products.php</code> to use the junction table for filtering.</p>";
echo "<p><strong>What was fixed:</strong></p>";
echo "<ul>";
echo "<li>✅ Old query: Only looked at <code>product_master.SubCategoryId</code></li>";
echo "<li>✅ New query: Looks at both junction table AND legacy field</li>";
echo "<li>✅ Now filtering will show products assigned to multiple categories</li>";
echo "</ul>";
echo "</div>";

// Get some subcategories for testing
$subCatQuery = "SELECT SubCategoryId, SubCategoryName FROM sub_category LIMIT 5";
$subCatResult = mysqli_query($mysqli, $subCatQuery);

echo "<div class='test-box'>";
echo "<h3>🔧 Test the Fixed Filtering</h3>";
echo "<p>Click on any subcategory below to test the AJAX filtering:</p>";

while ($row = mysqli_fetch_assoc($subCatResult)) {
    echo "<button onclick=\"testFilter({$row['SubCategoryId']}, '{$row['SubCategoryName']}')\">";
    echo htmlspecialchars($row['SubCategoryName']);
    echo "</button>";
}

echo "<div id='filter-results'></div>";
echo "</div>";

echo "<div class='info-box'>";
echo "<h3>📋 How to Test</h3>";
echo "<ol>";
echo "<li><strong>Assign a product to multiple categories first:</strong>";
echo "<ul>";
echo "<li>Go to <a href='cms/products.php' target='_blank'>CMS Products</a></li>";
echo "<li>Edit any product and assign it to multiple subcategories</li>";
echo "<li>Save the product</li>";
echo "</ul></li>";
echo "<li><strong>Test the filtering:</strong>";
echo "<ul>";
echo "<li>Go to <a href='products.php' target='_blank'>Products Page</a></li>";
echo "<li>Use the filter checkboxes on the left side</li>";
echo "<li>The product should now appear when filtering by any of its assigned categories</li>";
echo "</ul></li>";
echo "<li><strong>Or use the test buttons above</strong> to simulate the AJAX calls</li>";
echo "</ol>";
echo "</div>";

echo "<div class='success-box'>";
echo "<h3>🎯 Expected Behavior</h3>";
echo "<p><strong>Before Fix:</strong> Product only appeared when filtering by its primary category</p>";
echo "<p><strong>After Fix:</strong> Product appears when filtering by ANY of its assigned categories</p>";
echo "<p><strong>Example:</strong> If Product A is assigned to Immunity Wellness + Diabetic Care + Digestive Wellness:</p>";
echo "<ul>";
echo "<li>✅ Filtering by Immunity Wellness → Shows Product A</li>";
echo "<li>✅ Filtering by Diabetic Care → Shows Product A</li>";
echo "<li>✅ Filtering by Digestive Wellness → Shows Product A</li>";
echo "</ul>";
echo "</div>";

?>

<script>
function testFilter(subCategoryId, subCategoryName) {
    const resultsDiv = document.getElementById('filter-results');
    resultsDiv.innerHTML = '<p>🔄 Testing filter for: <strong>' + subCategoryName + '</strong>...</p>';
    
    // Simulate the AJAX call that the filtering system makes
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "exe_files/fetch_products.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            resultsDiv.innerHTML = '<div class="filter-result">' +
                '<h4>✅ Filter Results for: ' + subCategoryName + '</h4>' +
                '<div style="max-height: 400px; overflow-y: auto;">' + xhr.responseText + '</div>' +
                '</div>';
        } else {
            resultsDiv.innerHTML = '<p>❌ Error testing filter</p>';
        }
    };
    
    // Send the same data format as the real filtering system
    const data = 'SubCategoryId=' + encodeURIComponent(JSON.stringify([subCategoryId]));
    xhr.send(data);
}
</script>

<?php
echo "<p style='margin-top: 30px; color: #666;'><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>
