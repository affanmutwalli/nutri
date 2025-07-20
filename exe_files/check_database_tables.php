<?php
header('Content-Type: application/json');

include('../database/dbconnection.php');

$obj = new main();
$mysqli = $obj->connection();

$requiredTables = [
    'customer_points',
    'points_transactions', 
    'rewards_catalog',
    'reward_redemptions',
    'points_config',
    'enhanced_coupons',
    'customer_coupons'
];

$optionalTables = [
    'customer_referrals'
];

$tableStatus = [];
$allRequired = true;

// Check required tables
foreach ($requiredTables as $table) {
    $query = "SHOW TABLES LIKE '$table'";
    $result = $mysqli->query($query);
    $exists = $result->num_rows > 0;
    
    $tableStatus[$table] = [
        'exists' => $exists,
        'required' => true,
        'status' => $exists ? 'OK' : 'MISSING'
    ];
    
    if (!$exists) {
        $allRequired = false;
    }
}

// Check optional tables
foreach ($optionalTables as $table) {
    $query = "SHOW TABLES LIKE '$table'";
    $result = $mysqli->query($query);
    $exists = $result->num_rows > 0;
    
    $tableStatus[$table] = [
        'exists' => $exists,
        'required' => false,
        'status' => $exists ? 'OK' : 'OPTIONAL'
    ];
}

// Check if rewards system is functional
$functional = true;
$functionalityTests = [];

try {
    // Test 1: Check if we can query customer_points
    if ($tableStatus['customer_points']['exists']) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM customer_points");
        $count = $result->fetch_assoc()['count'];
        $functionalityTests['customer_points_query'] = [
            'test' => 'Query customer_points table',
            'status' => 'PASS',
            'message' => "Found $count customer point records"
        ];
    } else {
        $functionalityTests['customer_points_query'] = [
            'test' => 'Query customer_points table',
            'status' => 'FAIL',
            'message' => 'Table does not exist'
        ];
        $functional = false;
    }
    
    // Test 2: Check if we can query rewards_catalog
    if ($tableStatus['rewards_catalog']['exists']) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM rewards_catalog WHERE is_active = 1");
        $count = $result->fetch_assoc()['count'];
        $functionalityTests['rewards_catalog_query'] = [
            'test' => 'Query rewards_catalog table',
            'status' => 'PASS',
            'message' => "Found $count active rewards"
        ];
    } else {
        $functionalityTests['rewards_catalog_query'] = [
            'test' => 'Query rewards_catalog table',
            'status' => 'FAIL',
            'message' => 'Table does not exist'
        ];
        $functional = false;
    }
    
    // Test 3: Check if RewardsSystem class can be loaded
    if (file_exists('../includes/RewardsSystem.php')) {
        include_once '../includes/RewardsSystem.php';
        $rewards = new RewardsSystem();
        $functionalityTests['rewards_class'] = [
            'test' => 'Load RewardsSystem class',
            'status' => 'PASS',
            'message' => 'RewardsSystem class loaded successfully'
        ];
    } else {
        $functionalityTests['rewards_class'] = [
            'test' => 'Load RewardsSystem class',
            'status' => 'FAIL',
            'message' => 'RewardsSystem.php file not found'
        ];
        $functional = false;
    }
    
} catch (Exception $e) {
    $functionalityTests['general_error'] = [
        'test' => 'General functionality',
        'status' => 'FAIL',
        'message' => 'Error: ' . $e->getMessage()
    ];
    $functional = false;
}

echo json_encode([
    'all_required_tables_exist' => $allRequired,
    'system_functional' => $functional,
    'tables' => $tableStatus,
    'functionality_tests' => $functionalityTests,
    'recommendations' => $allRequired ? 
        ['System is ready to use!'] : 
        ['Run setup_basic_rewards.php to create missing tables', 'Check database permissions']
]);

$mysqli->close();
?>
