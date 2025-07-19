<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup My Nutrify Rewards System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #27ae60; background: #d5f4e6; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #e74c3c; background: #fdf2f2; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #3498db; background: #ebf3fd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .step { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #ff8c00; }
        h1 { color: #2d5016; }
        h2 { color: #ff8c00; }
        .btn { background: #ff8c00; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2d5016; }
    </style>
</head>
<body>
    <h1>🎁 My Nutrify Rewards System Setup</h1>
    
    <?php
    // Include database configuration
    include_once 'database/dbdetails.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
        echo "<h2>Setting up Rewards System...</h2>";
        
        try {
            // Connect to database
            $mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
            if ($mysqli->connect_error) {
                throw new Exception("Connection failed: " . $mysqli->connect_error);
            }
            $mysqli->set_charset("utf8mb4");
            
            // Read and execute SQL file
            $sqlFile = 'database/rewards_system_schema.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception("SQL file not found: $sqlFile");
            }
            
            $sql = file_get_contents($sqlFile);
            
            // Split SQL into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($statements as $statement) {
                if (empty($statement) || strpos($statement, '--') === 0) {
                    continue;
                }
                
                try {
                    if ($mysqli->query($statement)) {
                        $successCount++;
                        
                        // Show specific success messages for important operations
                        if (strpos($statement, 'CREATE TABLE') !== false) {
                            preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches);
                            $tableName = $matches[1] ?? 'table';
                            echo "<div class='success'>✅ Created table: $tableName</div>";
                        } elseif (strpos($statement, 'INSERT INTO points_config') !== false) {
                            echo "<div class='success'>✅ Inserted default points configuration</div>";
                        } elseif (strpos($statement, 'INSERT INTO rewards_catalog') !== false) {
                            echo "<div class='success'>✅ Inserted default rewards catalog</div>";
                        }
                    } else {
                        throw new Exception($mysqli->error);
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
                }
            }
            
            echo "<div class='step'>";
            echo "<h3>Setup Complete!</h3>";
            echo "<p><strong>Successfully executed:</strong> $successCount statements</p>";
            if ($errorCount > 0) {
                echo "<p><strong>Errors encountered:</strong> $errorCount statements</p>";
            }
            echo "</div>";
            
            // Test the rewards system
            echo "<h2>Testing Rewards System...</h2>";
            
            include_once 'includes/RewardsSystem.php';
            $rewards = new RewardsSystem();
            
            echo "<div class='success'>✅ RewardsSystem class loaded successfully</div>";
            
            // Test with a sample customer (if exists)
            $testQuery = "SELECT CustomerId FROM customer_master LIMIT 1";
            $result = $mysqli->query($testQuery);
            
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $testCustomerId = $row['CustomerId'];
                
                $pointsData = $rewards->getCustomerPoints($testCustomerId);
                echo "<div class='success'>✅ Successfully retrieved points for customer ID: $testCustomerId</div>";
                echo "<div class='info'>Points: {$pointsData['total_points']}, Tier: {$pointsData['tier_level']}</div>";
                
                $referralCode = $rewards->getCustomerReferralCode($testCustomerId);
                echo "<div class='success'>✅ Generated referral code: $referralCode</div>";
            } else {
                echo "<div class='info'>ℹ️ No customers found for testing, but system is ready</div>";
            }
            
            echo "<div class='step'>";
            echo "<h3>🎉 Rewards System Setup Complete!</h3>";
            echo "<p>Your dynamic rewards system is now ready to use. Features include:</p>";
            echo "<ul>";
            echo "<li>✅ Dynamic points earning and redemption</li>";
            echo "<li>✅ Customer tier system (Bronze, Silver, Gold, Platinum)</li>";
            echo "<li>✅ Referral program</li>";
            echo "<li>✅ Points transaction history</li>";
            echo "<li>✅ Configurable rewards catalog</li>";
            echo "</ul>";
            echo "<p><a href='index.php' class='btn'>Go to Website</a></p>";
            echo "</div>";
            
            $mysqli->close();
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Setup failed: " . $e->getMessage() . "</div>";
        }
    } else {
        ?>
        
        <div class="info">
            <h2>About the Rewards System</h2>
            <p>This will set up a complete dynamic rewards system for My Nutrify with the following features:</p>
            <ul>
                <li><strong>Dynamic Points:</strong> Customers earn points for purchases, reviews, and referrals</li>
                <li><strong>Tier System:</strong> Bronze, Silver, Gold, and Platinum tiers based on lifetime points</li>
                <li><strong>Rewards Catalog:</strong> Discount coupons, free shipping, and cashback offers</li>
                <li><strong>Referral Program:</strong> Customers can refer friends and earn bonus points</li>
                <li><strong>Transaction History:</strong> Complete tracking of all points activities</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>Step 1: Database Setup</h3>
            <p>This will create the following tables:</p>
            <ul>
                <li><code>customer_points</code> - Store customer points and tier information</li>
                <li><code>points_transactions</code> - Track all points earning and redemption</li>
                <li><code>rewards_catalog</code> - Available rewards for redemption</li>
                <li><code>reward_redemptions</code> - Track customer reward usage</li>
                <li><code>customer_referrals</code> - Referral system tracking</li>
                <li><code>points_config</code> - System configuration settings</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>Step 2: Default Configuration</h3>
            <p>The system will be configured with:</p>
            <ul>
                <li><strong>Points Rate:</strong> 3 points per ₹100 spent</li>
                <li><strong>Signup Bonus:</strong> 25 points</li>
                <li><strong>Review Bonus:</strong> 25 points</li>
                <li><strong>Referral Bonus:</strong> 100 points for referrer, 50 for referred</li>
                <li><strong>Default Rewards:</strong> ₹50, ₹100, ₹200 discount coupons</li>
            </ul>
        </div>
        
        <form method="POST">
            <button type="submit" name="setup" class="btn">🚀 Setup Rewards System</button>
        </form>
        
        <?php
    }
    ?>
</body>
</html>
