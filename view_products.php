<?php
// View available products and their documents
include('database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

if (!$mysqli) {
    die("Database connection failed");
}

echo "<h1>📦 Your Products & Documents</h1>";

try {
    // Get products with document counts
    $query = "
    SELECT 
        pm.ProductId, 
        pm.ProductName, 
        pm.ProductCode,
        pm.PhotoPath,
        COUNT(pd.document_id) as document_count
    FROM product_master pm 
    LEFT JOIN product_documents pd ON pm.ProductId = pd.product_id AND pd.is_active = 1
    GROUP BY pm.ProductId, pm.ProductName, pm.ProductCode, pm.PhotoPath
    ORDER BY pm.ProductId 
    LIMIT 10";
    
    $result = $mysqli->query($query);
    
    if ($result->num_rows > 0) {
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>";
        
        while ($product = $result->fetch_assoc()) {
            echo "<div style='border: 2px solid #ddd; border-radius: 10px; padding: 20px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>";
            
            // Product image
            if (!empty($product['PhotoPath'])) {
                echo "<img src='" . htmlspecialchars($product['PhotoPath']) . "' alt='Product Image' style='width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;'>";
            }
            
            echo "<h3 style='color: #333; margin-bottom: 10px;'>" . htmlspecialchars($product['ProductName']) . "</h3>";
            echo "<p><strong>ID:</strong> " . $product['ProductId'] . "</p>";
            echo "<p><strong>Code:</strong> " . htmlspecialchars($product['ProductCode']) . "</p>";
            echo "<p><strong>Documents:</strong> " . $product['document_count'] . " PDF(s)</p>";
            
            echo "<div style='margin-top: 15px;'>";
            echo "<a href='product_details.php?id=" . $product['ProductId'] . "' target='_blank' style='background: #EA652D; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View Page</a>";
            echo "<a href='cms/products.php?edit=" . $product['ProductId'] . "' target='_blank' style='background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;'>Edit in CMS</a>";
            echo "</div>";
            
            // Show documents if any
            if ($product['document_count'] > 0) {
                $docQuery = "SELECT document_title, document_type, file_path FROM product_documents WHERE product_id = ? AND is_active = 1";
                $docStmt = $mysqli->prepare($docQuery);
                $docStmt->bind_param("i", $product['ProductId']);
                $docStmt->execute();
                $docResult = $docStmt->get_result();
                
                echo "<div style='margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;'>";
                echo "<h4 style='color: #666; margin-bottom: 10px;'>📄 Documents:</h4>";
                
                while ($doc = $docResult->fetch_assoc()) {
                    echo "<div style='margin-bottom: 8px;'>";
                    echo "<a href='cms/" . htmlspecialchars($doc['file_path']) . "' target='_blank' style='color: #EA652D; text-decoration: none;'>";
                    echo "📄 " . htmlspecialchars($doc['document_title']);
                    echo "</a>";
                    echo " <span style='color: #EA652D; font-weight: 600; font-size: 30px;'>(" . str_replace('_', ' ', ucfirst($doc['document_type'])) . ")</span>";
                    echo "</div>";
                }
                echo "</div>";
                
                $docStmt->close();
            }
            
            echo "</div>";
        }
        
        echo "</div>";
        
    } else {
        echo "<p style='color: #666; text-align: center; margin: 40px 0;'>No products found in your database.</p>";
        echo "<p style='text-align: center;'><a href='cms/products.php' style='background: #EA652D; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>Add Products in CMS</a></p>";
    }
    
    echo "<hr style='margin: 40px 0;'>";
    echo "<h2>🔧 Quick Actions:</h2>";
    echo "<div style='display: flex; gap: 15px; flex-wrap: wrap;'>";
    echo "<a href='test_upload.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px;'>📤 Upload PDF</a>";
    echo "<a href='cms/products.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px;'>🛠️ Manage Products</a>";
    echo "<a href='add_existing_pdf.php' style='background: #ffc107; color: black; padding: 12px 20px; text-decoration: none; border-radius: 6px;'>📄 Add Existing PDF</a>";
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

h1, h2, h3, h4 {
    color: #333;
}

a {
    transition: all 0.3s ease;
}

a:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}
</style>
