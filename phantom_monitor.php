<?php
// Real-time phantom product monitor and eliminator
// This script can be called via AJAX or cron job

header('Content-Type: application/json');
require_once 'database/dbconnection.php';

function eliminatePhantomProducts() {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        return ['status' => 'error', 'message' => 'Database connection failed'];
    }
    
    // Define all phantom product patterns
    $phantomPatterns = [
        "ProductName = 'N/A'",
        "ProductName LIKE '%N/A%'",
        "ProductName = ''",
        "ProductName IS NULL",
        "ProductCode = 'MN-XX-000'",
        "ProductCode = 'MN-SJ100'",
        "ProductCode LIKE 'MN-XX-%'",
        "ProductCode LIKE 'MN-SJ%'",
        "ProductCode LIKE '%XX-000%'",
        "ProductCode LIKE '%SJ100%'",
        "(ProductName = 'N/A' AND ProductCode LIKE 'MN-%')"
    ];
    
    $whereClause = implode(' OR ', $phantomPatterns);
    $phantomQuery = "SELECT ProductId, ProductName, ProductCode FROM product_master WHERE " . $whereClause;
    
    $result = $mysqli->query($phantomQuery);
    $eliminatedCount = 0;
    $phantomProducts = [];
    
    if ($result && $result->num_rows > 0) {
        $phantomIds = [];
        while ($row = $result->fetch_assoc()) {
            $phantomIds[] = $row['ProductId'];
            $phantomProducts[] = $row;
        }
        
        if (!empty($phantomIds)) {
            $phantomIdsList = implode(',', array_map('intval', $phantomIds));
            
            // Start transaction
            $mysqli->autocommit(FALSE);
            
            try {
                // Remove from all related tables
                $tables = ['cart', 'order_details', 'product_images', 'product_reviews', 'product_price', 'wishlist'];
                
                foreach ($tables as $table) {
                    $deleteQuery = "DELETE FROM $table WHERE ProductId IN ($phantomIdsList)";
                    $mysqli->query($deleteQuery);
                }
                
                // Remove from combos
                $mysqli->query("DELETE FROM dynamic_combos WHERE product1_id IN ($phantomIdsList)");
                $mysqli->query("DELETE FROM dynamic_combos WHERE product2_id IN ($phantomIdsList)");
                
                // Remove from product_master
                $deleteProducts = "DELETE FROM product_master WHERE ProductId IN ($phantomIdsList)";
                if ($mysqli->query($deleteProducts)) {
                    $eliminatedCount = $mysqli->affected_rows;
                }
                
                $mysqli->commit();
                $mysqli->autocommit(TRUE);
                
            } catch (Exception $e) {
                $mysqli->rollback();
                $mysqli->autocommit(TRUE);
                return ['status' => 'error', 'message' => 'Elimination failed: ' . $e->getMessage()];
            }
        }
    }
    
    return [
        'status' => 'success',
        'eliminated_count' => $eliminatedCount,
        'phantom_products' => $phantomProducts,
        'message' => $eliminatedCount > 0 ? "Eliminated $eliminatedCount phantom products" : "No phantom products found"
    ];
}

// Execute elimination
$result = eliminatePhantomProducts();
echo json_encode($result);
?>
