<?php
// Test PDF file access
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed");
}

echo "<h2>🔍 Testing PDF File Access</h2>";

try {
    // Get all documents with their paths
    $result = $mysqli->query("SELECT pd.*, pm.ProductName FROM product_documents pd LEFT JOIN product_master pm ON pd.product_id = pm.ProductId WHERE pd.is_active = 1");
    
    if ($result->num_rows > 0) {
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;'>";
        
        while ($doc = $result->fetch_assoc()) {
            echo "<div style='border: 2px solid #ddd; padding: 20px; border-radius: 10px; background: white;'>";
            
            echo "<h3 style='color: #333; margin-bottom: 15px;'>" . htmlspecialchars($doc['document_title']) . "</h3>";
            echo "<p><strong>Product:</strong> " . htmlspecialchars($doc['ProductName'] ?? 'Unknown') . "</p>";
            echo "<p><strong>Type:</strong> <span style='color: #EA652D; font-weight: 600; font-size: 30px;'>" . str_replace('_', ' ', ucfirst($doc['document_type'])) . "</span></p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($doc['file_name']) . "</p>";
            
            // Test different path combinations
            $storedPath = $doc['file_path'];
            $testPaths = [
                "cms/" . $storedPath,
                $storedPath,
                "cms/docs/" . basename($doc['file_name']),
                "cms/docs/products/lab_reports/" . basename($doc['file_name'])
            ];
            
            echo "<div style='margin: 15px 0;'>";
            echo "<h4 style='color: #666;'>🔗 Access Tests:</h4>";
            
            $workingPath = null;
            foreach ($testPaths as $testPath) {
                $exists = file_exists($testPath);
                $color = $exists ? 'green' : 'red';
                $icon = $exists ? '✅' : '❌';
                
                echo "<div style='margin: 8px 0; padding: 8px; background: #f9f9f9; border-radius: 4px;'>";
                echo "<span style='color: $color;'>$icon</span> ";
                echo "<code style='background: #eee; padding: 2px 6px; border-radius: 3px;'>" . htmlspecialchars($testPath) . "</code>";
                
                if ($exists) {
                    $workingPath = $testPath;
                    $fileSize = filesize($testPath);
                    echo " <small style='color: #666;'>(" . number_format($fileSize / 1024, 1) . " KB)</small>";
                    echo " <a href='" . $testPath . "' target='_blank' style='color: #EA652D; margin-left: 10px;'>🔗 Open</a>";
                }
                echo "</div>";
            }
            
            if ($workingPath) {
                echo "<div style='margin-top: 15px; padding: 10px; background: #d4edda; border-radius: 5px; border-left: 4px solid #28a745;'>";
                echo "<strong style='color: #155724;'>✅ Working Path Found!</strong><br>";
                echo "<a href='" . $workingPath . "' target='_blank' style='background: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 8px;'>📄 View PDF</a>";
                echo "</div>";
            } else {
                echo "<div style='margin-top: 15px; padding: 10px; background: #f8d7da; border-radius: 5px; border-left: 4px solid #dc3545;'>";
                echo "<strong style='color: #721c24;'>❌ No Working Path Found</strong><br>";
                echo "<small>The PDF file may need to be re-uploaded or moved to the correct location.</small>";
                echo "</div>";
            }
            
            echo "</div>";
            echo "</div>";
        }
        
        echo "</div>";
        
    } else {
        echo "<p style='text-align: center; color: #666; margin: 40px 0;'>No documents found in database.</p>";
        echo "<p style='text-align: center;'><a href='test_upload.php' style='background: #EA652D; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>Upload Test PDF</a></p>";
    }
    
    echo "<hr style='margin: 40px 0;'>";
    echo "<h3>🛠️ Quick Actions:</h3>";
    echo "<div style='display: flex; gap: 15px; flex-wrap: wrap;'>";
    echo "<a href='add_existing_pdf.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📄 Add Existing PDF</a>";
    echo "<a href='test_upload.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📤 Upload New PDF</a>";
    echo "<a href='view_products.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👀 View Products</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}

code {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

a {
    transition: all 0.3s ease;
}

a:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}
</style>
