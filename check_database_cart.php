<?php
// Check Database Cart for Phantom Products
// This will help identify if phantom products are coming from the database cart

// Include database configuration and connection
include_once 'cms/includes/psl-config.php';

echo "<h2>🔍 Database Cart Investigation</h2>\n";

try {
    // Direct database connection
    $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    // Step 1: Check all cart records
    echo "<h3>Step 1: All Database Cart Records</h3>\n";
    
    $cartQuery = "
        SELECT 
            c.id,
            c.CustomerId,
            c.ProductId,
            c.Quantity,
            c.Price,
            c.CreationDate,
            c.UpdatedDate,
            pm.ProductName,
            pm.ProductCode,
            cm.Name as CustomerName
        FROM cart c
        LEFT JOIN product_master pm ON c.ProductId = pm.ProductId
        LEFT JOIN customer_master cm ON c.CustomerId = cm.CustomerId
        ORDER BY c.UpdatedDate DESC
        LIMIT 20
    ";
    
    $cartResult = $mysqli->query($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Cart ID</th><th>Customer ID</th><th>Customer Name</th><th>Product ID</th><th>Product Name</th><th>Product Code</th><th>Quantity</th><th>Price</th><th>Created</th><th>Updated</th></tr>\n";
        
        while ($cart = $cartResult->fetch_assoc()) {
            $isPhantom = (strpos($cart['ProductCode'], 'AC') !== false || 
                         strpos($cart['ProductCode'], 'SC') !== false || 
                         empty($cart['ProductCode']));
            $bgColor = $isPhantom ? 'background-color: #ffeeee;' : '';
            
            echo "<tr style='$bgColor'>";
            echo "<td>" . htmlspecialchars($cart['id']) . "</td>";
            echo "<td>" . htmlspecialchars($cart['CustomerId']) . "</td>";
            echo "<td>" . htmlspecialchars($cart['CustomerName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($cart['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($cart['ProductName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($cart['ProductCode'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($cart['Quantity']) . "</td>";
            echo "<td>₹" . number_format($cart['Price'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($cart['CreationDate']) . "</td>";
            echo "<td>" . htmlspecialchars($cart['UpdatedDate']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No records in database cart</p>\n";
    }
    
    // Step 2: Check for orphaned cart records (products that don't exist)
    echo "<h3>Step 2: Orphaned Cart Records</h3>\n";
    
    $orphanQuery = "
        SELECT 
            c.id,
            c.CustomerId,
            c.ProductId,
            c.Quantity,
            c.Price,
            c.CreationDate
        FROM cart c
        LEFT JOIN product_master pm ON c.ProductId = pm.ProductId
        WHERE pm.ProductId IS NULL
        ORDER BY c.CreationDate DESC
    ";
    
    $orphanResult = $mysqli->query($orphanQuery);
    
    if ($orphanResult && $orphanResult->num_rows > 0) {
        echo "<p style='color: red;'>❌ Found orphaned cart records (products that don't exist):</p>\n";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Cart ID</th><th>Customer ID</th><th>Product ID</th><th>Quantity</th><th>Price</th><th>Created</th><th>Action</th></tr>\n";
        
        while ($orphan = $orphanResult->fetch_assoc()) {
            echo "<tr style='background-color: #ffeeee;'>";
            echo "<td>" . htmlspecialchars($orphan['id']) . "</td>";
            echo "<td>" . htmlspecialchars($orphan['CustomerId']) . "</td>";
            echo "<td>" . htmlspecialchars($orphan['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($orphan['Quantity']) . "</td>";
            echo "<td>₹" . number_format($orphan['Price'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($orphan['CreationDate']) . "</td>";
            echo "<td><a href='?action=delete_orphan&cart_id=" . $orphan['id'] . "' style='color: red;'>Delete</a></td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No orphaned cart records found</p>\n";
    }
    
    // Step 3: Check for phantom products in cart
    echo "<h3>Step 3: Phantom Products in Cart</h3>\n";
    
    $phantomCartQuery = "
        SELECT 
            c.id,
            c.CustomerId,
            c.ProductId,
            c.Quantity,
            c.Price,
            pm.ProductName,
            pm.ProductCode
        FROM cart c
        LEFT JOIN product_master pm ON c.ProductId = pm.ProductId
        WHERE pm.ProductCode LIKE '%AC%' OR pm.ProductCode LIKE '%SC%'
        ORDER BY c.UpdatedDate DESC
    ";
    
    $phantomCartResult = $mysqli->query($phantomCartQuery);
    
    if ($phantomCartResult && $phantomCartResult->num_rows > 0) {
        echo "<p style='color: red;'>❌ Found phantom products in cart:</p>\n";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Cart ID</th><th>Customer ID</th><th>Product ID</th><th>Product Name</th><th>Product Code</th><th>Quantity</th><th>Price</th><th>Action</th></tr>\n";
        
        while ($phantom = $phantomCartResult->fetch_assoc()) {
            echo "<tr style='background-color: #ffeeee;'>";
            echo "<td>" . htmlspecialchars($phantom['id']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['CustomerId']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['ProductId']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['ProductName']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['ProductCode']) . "</td>";
            echo "<td>" . htmlspecialchars($phantom['Quantity']) . "</td>";
            echo "<td>₹" . number_format($phantom['Price'], 2) . "</td>";
            echo "<td><a href='?action=delete_phantom&cart_id=" . $phantom['id'] . "' style='color: red;'>Delete</a></td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p style='color: green;'>✓ No phantom products found in cart</p>\n";
    }
    
    // Step 4: Handle cleanup actions
    if (isset($_GET['action'])) {
        echo "<h3>Step 4: Cleanup Action</h3>\n";
        
        if ($_GET['action'] == 'delete_orphan' && isset($_GET['cart_id'])) {
            $cartId = (int)$_GET['cart_id'];
            $deleteQuery = "DELETE FROM cart WHERE id = ?";
            $stmt = $mysqli->prepare($deleteQuery);
            $stmt->bind_param("i", $cartId);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Deleted orphaned cart record ID: $cartId</p>\n";
            } else {
                echo "<p style='color: red;'>❌ Failed to delete cart record: " . $stmt->error . "</p>\n";
            }
        }
        
        if ($_GET['action'] == 'delete_phantom' && isset($_GET['cart_id'])) {
            $cartId = (int)$_GET['cart_id'];
            $deleteQuery = "DELETE FROM cart WHERE id = ?";
            $stmt = $mysqli->prepare($deleteQuery);
            $stmt->bind_param("i", $cartId);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Deleted phantom cart record ID: $cartId</p>\n";
            } else {
                echo "<p style='color: red;'>❌ Failed to delete cart record: " . $stmt->error . "</p>\n";
            }
        }
        
        if ($_GET['action'] == 'cleanup_all') {
            // Delete all orphaned records
            $cleanupQuery = "
                DELETE c FROM cart c
                LEFT JOIN product_master pm ON c.ProductId = pm.ProductId
                WHERE pm.ProductId IS NULL
            ";
            
            if ($mysqli->query($cleanupQuery)) {
                $deletedCount = $mysqli->affected_rows;
                echo "<p style='color: green;'>✅ Cleaned up $deletedCount orphaned cart records</p>\n";
            } else {
                echo "<p style='color: red;'>❌ Failed to cleanup orphaned records: " . $mysqli->error . "</p>\n";
            }
            
            // Delete phantom products from cart
            $phantomCleanupQuery = "
                DELETE c FROM cart c
                LEFT JOIN product_master pm ON c.ProductId = pm.ProductId
                WHERE pm.ProductCode LIKE '%AC%' OR pm.ProductCode LIKE '%SC%'
            ";
            
            if ($mysqli->query($phantomCleanupQuery)) {
                $deletedCount = $mysqli->affected_rows;
                echo "<p style='color: green;'>✅ Cleaned up $deletedCount phantom cart records</p>\n";
            } else {
                echo "<p style='color: red;'>❌ Failed to cleanup phantom records: " . $mysqli->error . "</p>\n";
            }
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>🛠️ Cleanup Actions</h3>\n";
echo "<div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p><strong>Available Actions:</strong></p>\n";
echo "<ul>\n";
echo "<li><a href='?action=cleanup_all' style='color: #d63384; font-weight: bold;'>🧹 Clean Up All Orphaned and Phantom Cart Records</a></li>\n";
echo "</ul>\n";
echo "<p><strong>Note:</strong> This will remove all cart records for products that don't exist or are phantom products.</p>\n";
echo "</div>\n";

echo "<h3>🎯 Investigation Results</h3>\n";
echo "<div style='background-color: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<p><strong>Root Cause Analysis:</strong></p>\n";
echo "<p>The phantom products are likely being added to orders through one of these mechanisms:</p>\n";
echo "<ul>\n";
echo "<li>🔍 <strong>Database Cart Persistence:</strong> Old phantom products stored in database cart</li>\n";
echo "<li>🔍 <strong>Session Cart Contamination:</strong> Phantom products in session cart</li>\n";
echo "<li>🔍 <strong>Checkout Page Logic:</strong> Hidden or duplicate checkout items</li>\n";
echo "<li>🔍 <strong>Cart Sync Issues:</strong> Problems when merging session and database carts</li>\n";
echo "</ul>\n";
echo "</div>\n";
?>
