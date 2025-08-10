<?php
// Fix PDF file paths in database
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed");
}

echo "<h2>🔧 Fixing PDF File Paths</h2>";

try {
    // Get all documents
    $result = $mysqli->query("SELECT document_id, file_path, file_name FROM product_documents");
    
    if ($result->num_rows > 0) {
        echo "<h3>📋 Current Documents:</h3>";
        
        while ($doc = $result->fetch_assoc()) {
            $currentPath = $doc['file_path'];
            $fileName = $doc['file_name'];
            $documentId = $doc['document_id'];
            
            echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
            echo "<p><strong>Document ID:</strong> " . $documentId . "</p>";
            echo "<p><strong>File Name:</strong> " . htmlspecialchars($fileName) . "</p>";
            echo "<p><strong>Current Path:</strong> " . htmlspecialchars($currentPath) . "</p>";
            
            // Check if file exists at current path
            $fullCurrentPath = "cms/" . $currentPath;
            if (file_exists($fullCurrentPath)) {
                echo "<p style='color: green;'>✅ File exists at: " . $fullCurrentPath . "</p>";
                echo "<p><a href='" . $fullCurrentPath . "' target='_blank' style='color: #EA652D;'>🔗 Test Link</a></p>";
            } else {
                echo "<p style='color: red;'>❌ File not found at: " . $fullCurrentPath . "</p>";
                
                // Try to find the file in different locations
                $possiblePaths = [
                    "cms/docs/" . $fileName,
                    "cms/docs/products/lab_reports/" . $fileName,
                    "cms/docs/products/certificates/" . $fileName,
                    "cms/docs/products/test_reports/" . $fileName,
                    "cms/docs/products/specifications/" . $fileName,
                    "cms/docs/products/other/" . $fileName
                ];
                
                $found = false;
                foreach ($possiblePaths as $testPath) {
                    if (file_exists($testPath)) {
                        echo "<p style='color: blue;'>🔍 Found file at: " . $testPath . "</p>";
                        
                        // Update database with correct path
                        $correctPath = str_replace('cms/', '', $testPath);
                        $updateStmt = $mysqli->prepare("UPDATE product_documents SET file_path = ? WHERE document_id = ?");
                        $updateStmt->bind_param("si", $correctPath, $documentId);
                        
                        if ($updateStmt->execute()) {
                            echo "<p style='color: green;'>✅ Updated database path to: " . $correctPath . "</p>";
                            echo "<p><a href='" . $testPath . "' target='_blank' style='color: #EA652D;'>🔗 Test Link</a></p>";
                        } else {
                            echo "<p style='color: red;'>❌ Failed to update database</p>";
                        }
                        $updateStmt->close();
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    echo "<p style='color: orange;'>⚠️ File not found in any expected location</p>";
                }
            }
            
            echo "</div>";
        }
        
    } else {
        echo "<p>No documents found in database.</p>";
    }
    
    echo "<hr>";
    echo "<h3>🔗 Test Your Documents:</h3>";
    echo "<p><a href='view_products.php' target='_blank' style='background: #EA652D; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View All Products & Documents</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>
