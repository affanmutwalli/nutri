<?php
/**
 * Manual Guest Checkout Setup
 * Use this if the automated setup fails
 */

include_once 'database/dbconnection.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Manual Guest Checkout Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #ec6504; border-bottom: 3px solid #ec6504; padding-bottom: 10px; }
    .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
    .btn { background: #ec6504; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
    .btn:hover { background: #d55a04; }
    .sql-command { background: #f8f9fa; padding: 10px; border-left: 4px solid #ec6504; margin: 10px 0; font-family: monospace; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔧 Manual Guest Checkout Setup</h1>";

try {
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }
    
    echo "<div class='info'>✅ Database connection successful</div>";
    
    if ($_POST['action'] ?? '' === 'setup') {
        echo "<h2>Executing Manual Setup...</h2>";
        
        // Step 1: Add columns one by one
        $columns = [
            'GuestName' => "ALTER TABLE order_master ADD COLUMN GuestName VARCHAR(255) NULL COMMENT 'Guest customer name'",
            'GuestEmail' => "ALTER TABLE order_master ADD COLUMN GuestEmail VARCHAR(255) NULL COMMENT 'Guest customer email'",
            'GuestPhone' => "ALTER TABLE order_master ADD COLUMN GuestPhone VARCHAR(20) NULL COMMENT 'Guest customer phone'"
        ];
        
        foreach ($columns as $columnName => $sql) {
            // Check if column exists
            $checkQuery = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'order_master' 
                          AND COLUMN_NAME = '$columnName'";
            
            $result = $mysqli->query($checkQuery);
            $row = $result->fetch_assoc();
            
            if ($row['count'] == 0) {
                if ($mysqli->query($sql)) {
                    echo "<div class='success'>✅ Added column: $columnName</div>";
                } else {
                    echo "<div class='error'>❌ Failed to add column $columnName: " . $mysqli->error . "</div>";
                }
            } else {
                echo "<div class='warning'>⚠️ Column $columnName already exists</div>";
            }
        }
        
        // Step 2: Add indexes
        $indexes = [
            'idx_guest_email' => "CREATE INDEX idx_guest_email ON order_master(GuestEmail)",
            'idx_guest_phone' => "CREATE INDEX idx_guest_phone ON order_master(GuestPhone)",
            'idx_customer_type' => "CREATE INDEX idx_customer_type ON order_master(CustomerType)"
        ];
        
        foreach ($indexes as $indexName => $sql) {
            // Check if index exists
            $checkQuery = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.STATISTICS 
                          WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'order_master' 
                          AND INDEX_NAME = '$indexName'";
            
            $result = $mysqli->query($checkQuery);
            $row = $result->fetch_assoc();
            
            if ($row['count'] == 0) {
                if ($mysqli->query($sql)) {
                    echo "<div class='success'>✅ Created index: $indexName</div>";
                } else {
                    echo "<div class='warning'>⚠️ Failed to create index $indexName (this is optional): " . $mysqli->error . "</div>";
                }
            } else {
                echo "<div class='warning'>⚠️ Index $indexName already exists</div>";
            }
        }
        
        // Step 3: Create views
        $guestOrdersView = "CREATE OR REPLACE VIEW guest_orders AS
        SELECT 
            OrderId,
            GuestName as CustomerName,
            GuestEmail as CustomerEmail,
            GuestPhone as CustomerPhone,
            OrderDate,
            Amount,
            PaymentStatus,
            OrderStatus,
            ShipAddress,
            PaymentType,
            CreatedAt,
            'Guest' as CustomerType
        FROM order_master 
        WHERE CustomerType = 'Guest' AND CustomerId = 0";
        
        if ($mysqli->query($guestOrdersView)) {
            echo "<div class='success'>✅ Created guest_orders view</div>";
        } else {
            echo "<div class='error'>❌ Failed to create guest_orders view: " . $mysqli->error . "</div>";
        }
        
        $allOrdersView = "CREATE OR REPLACE VIEW all_orders_unified AS
        SELECT 
            om.OrderId,
            CASE 
                WHEN om.CustomerType = 'Guest' THEN om.GuestName
                ELSE cm.Name
            END as CustomerName,
            CASE 
                WHEN om.CustomerType = 'Guest' THEN om.GuestEmail
                ELSE cm.Email
            END as CustomerEmail,
            CASE 
                WHEN om.CustomerType = 'Guest' THEN om.GuestPhone
                ELSE cm.MobileNo
            END as CustomerPhone,
            om.CustomerId,
            om.CustomerType,
            om.OrderDate,
            om.Amount,
            om.PaymentStatus,
            om.OrderStatus,
            om.ShipAddress,
            om.PaymentType,
            om.TransactionId,
            om.CreatedAt
        FROM order_master om
        LEFT JOIN customer_master cm ON om.CustomerId = cm.CustomerId AND om.CustomerType != 'Guest'
        ORDER BY om.CreatedAt DESC";
        
        if ($mysqli->query($allOrdersView)) {
            echo "<div class='success'>✅ Created all_orders_unified view</div>";
        } else {
            echo "<div class='error'>❌ Failed to create all_orders_unified view: " . $mysqli->error . "</div>";
        }
        
        // Step 4: Test the setup
        echo "<h2>Testing Setup...</h2>";
        
        // Check if all columns exist
        $result = $mysqli->query("DESCRIBE order_master");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        
        $guestColumns = ['GuestName', 'GuestEmail', 'GuestPhone'];
        $missingColumns = array_diff($guestColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "<div class='success'>✅ All guest columns are present</div>";
        } else {
            echo "<div class='error'>❌ Missing columns: " . implode(', ', $missingColumns) . "</div>";
        }
        
        // Test views
        $views = ['guest_orders', 'all_orders_unified'];
        foreach ($views as $view) {
            $result = $mysqli->query("SHOW TABLES LIKE '$view'");
            if ($result->num_rows > 0) {
                echo "<div class='success'>✅ View '$view' exists</div>";
            } else {
                echo "<div class='error'>❌ View '$view' does not exist</div>";
            }
        }
        
        echo "<div class='success'><h3>🎉 Manual setup completed!</h3></div>";
        
    } else {
        // Show setup form
        echo "<div class='info'>";
        echo "<h3>📋 Manual Setup Instructions</h3>";
        echo "<p>This script will manually add the required columns and views for guest checkout.</p>";
        echo "<p><strong>What will be added:</strong></p>";
        echo "<ul>";
        echo "<li>GuestName column to order_master table</li>";
        echo "<li>GuestEmail column to order_master table</li>";
        echo "<li>GuestPhone column to order_master table</li>";
        echo "<li>Database indexes for performance</li>";
        echo "<li>Views for guest order management</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<form method='POST'>";
        echo "<input type='hidden' name='action' value='setup'>";
        echo "<button type='submit' class='btn'>🚀 Run Manual Setup</button>";
        echo "</form>";
        
        echo "<div class='warning'>";
        echo "<h3>⚠️ Alternative: Run SQL Commands Manually</h3>";
        echo "<p>If the automated setup fails, you can run these SQL commands manually in your database:</p>";
        echo "</div>";
        
        echo "<div class='sql-command'>";
        echo "<strong>1. Add Guest Columns:</strong><br>";
        echo "ALTER TABLE order_master ADD COLUMN GuestName VARCHAR(255) NULL;<br>";
        echo "ALTER TABLE order_master ADD COLUMN GuestEmail VARCHAR(255) NULL;<br>";
        echo "ALTER TABLE order_master ADD COLUMN GuestPhone VARCHAR(20) NULL;";
        echo "</div>";
        
        echo "<div class='sql-command'>";
        echo "<strong>2. Add Indexes (Optional):</strong><br>";
        echo "CREATE INDEX idx_guest_email ON order_master(GuestEmail);<br>";
        echo "CREATE INDEX idx_guest_phone ON order_master(GuestPhone);<br>";
        echo "CREATE INDEX idx_customer_type ON order_master(CustomerType);";
        echo "</div>";
        
        echo "<div class='sql-command'>";
        echo "<strong>3. Create Views:</strong><br>";
        echo "<pre>CREATE OR REPLACE VIEW guest_orders AS
SELECT 
    OrderId,
    GuestName as CustomerName,
    GuestEmail as CustomerEmail,
    GuestPhone as CustomerPhone,
    OrderDate,
    Amount,
    PaymentStatus,
    OrderStatus,
    ShipAddress,
    PaymentType,
    CreatedAt,
    'Guest' as CustomerType
FROM order_master 
WHERE CustomerType = 'Guest' AND CustomerId = 0;</pre>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div></body></html>";
?>
