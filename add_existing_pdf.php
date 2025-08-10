<?php
// Script to add your existing HTH-17199 Test Report PDF
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed");
}

echo "<h2>📄 Adding Your Existing PDF to the System</h2>";

try {
    // Get the first product for testing
    $result = $mysqli->query("SELECT ProductId, ProductName FROM product_master ORDER BY ProductId LIMIT 1");
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $productId = $product['ProductId'];
        $productName = $product['ProductName'];
        
        echo "<p><strong>Target Product:</strong> " . htmlspecialchars($productName) . " (ID: " . $productId . ")</p>";
        
        // Check if the PDF file exists
        $sourcePath = 'cms/docs/HTH-17199 Test Report (Briovinegars).pdf';
        
        if (file_exists($sourcePath)) {
            echo "<p>✅ Found your PDF file: " . $sourcePath . "</p>";
            
            // Create destination directory
            $destinationDir = 'cms/docs/products/lab_reports/';
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
                echo "<p>✅ Created upload directory</p>";
            }
            
            // Copy file to proper location
            $destinationFile = 'HTH-17199_Test_Report_Briovinegars.pdf';
            $destinationPath = $destinationDir . $destinationFile;
            
            if (!file_exists($destinationPath)) {
                if (copy($sourcePath, $destinationPath)) {
                    echo "<p>✅ PDF copied to: " . $destinationPath . "</p>";
                } else {
                    echo "<p>❌ Failed to copy PDF file</p>";
                    exit;
                }
            } else {
                echo "<p>ℹ️ PDF already exists at destination</p>";
            }
            
            // Check if already in database
            $checkStmt = $mysqli->prepare("SELECT document_id FROM product_documents WHERE product_id = ? AND file_name = ?");
            $fileName = "HTH-17199 Test Report (Briovinegars).pdf";
            $checkStmt->bind_param("is", $productId, $fileName);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                echo "<p>ℹ️ Document already exists in database</p>";
                $doc = $checkResult->fetch_assoc();
                echo "<p><strong>Document ID:</strong> " . $doc['document_id'] . "</p>";
            } else {
                // Add to database
                $documentTitle = "HTH-17199 Test Report (Briovinegars)";
                $documentType = "lab_report";
                $filePath = "docs/products/lab_reports/" . $destinationFile;
                $fileSize = filesize($destinationPath);
                $mimeType = "application/pdf";
                
                $stmt = $mysqli->prepare("INSERT INTO product_documents (product_id, document_title, document_type, file_name, file_path, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssis", $productId, $documentTitle, $documentType, $fileName, $filePath, $fileSize, $mimeType);
                
                if ($stmt->execute()) {
                    $documentId = $mysqli->insert_id;
                    echo "<p style='color: green;'>✅ Document added to database successfully!</p>";
                    echo "<p><strong>Document ID:</strong> " . $documentId . "</p>";
                    echo "<p><strong>File Size:</strong> " . number_format($fileSize / 1024, 2) . " KB</p>";
                } else {
                    echo "<p style='color: red;'>❌ Database error: " . $stmt->error . "</p>";
                }
                $stmt->close();
            }
            $checkStmt->close();
            
        } else {
            echo "<p style='color: red;'>❌ PDF file not found at: " . $sourcePath . "</p>";
            echo "<p>Please make sure your HTH-17199 Test Report PDF is in the cms/docs/ folder</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ No products found in database</p>";
        echo "<p>Please add some products first through the CMS</p>";
    }
    
    echo "<hr>";
    echo "<h3>🔗 Quick Links:</h3>";
    echo "<ul>";
    echo "<li><a href='test_upload.php' target='_blank'>Test Upload More PDFs</a></li>";
    echo "<li><a href='cms/products.php' target='_blank'>Manage Products in CMS</a></li>";
    if (isset($productId)) {
        echo "<li><a href='product_details.php?id=" . $productId . "' target='_blank'>View Product Page</a></li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$mysqli->close();
?>
