<?php
session_start();

// Simulate logged in user for testing
if (!isset($_SESSION['CustomerId'])) {
    $_SESSION['CustomerId'] = 1; // Test customer ID
    $_SESSION['CustomerName'] = 'Test Customer';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Rewards Modal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .test-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .test-header h1 {
            color: #2d5016;
            margin-bottom: 10px;
        }
        
        .test-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        
        .test-btn {
            background: linear-gradient(135deg, #ff8c00 0%, #2d5016 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 140, 0, 0.3);
        }
        
        .status-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #ff8c00;
        }
        
        .status-box h3 {
            margin-top: 0;
            color: #2d5016;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-list li i {
            color: #28a745;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🎁 Rewards Modal Test</h1>
            <p>Test the beautiful rewards modal for logged-in users</p>
        </div>
        
        <div class="status-box">
            <h3>✅ Session Status</h3>
            <p><strong>Customer ID:</strong> <?php echo $_SESSION['CustomerId']; ?></p>
            <p><strong>Customer Name:</strong> <?php echo $_SESSION['CustomerName']; ?></p>
            <p><strong>Status:</strong> <span style="color: green;">Logged In</span></p>
        </div>
        
        <div class="test-buttons">
            <button class="test-btn" onclick="openRewardsModal()">
                <i class="fas fa-coins"></i> View My Points
            </button>
            <button class="test-btn" onclick="openRedeemModal()">
                <i class="fas fa-gift"></i> Redeem Rewards
            </button>
            <a href="rewards.php" class="test-btn" style="text-decoration: none;">
                <i class="fas fa-external-link-alt"></i> Go to Rewards Page
            </a>
        </div>
        
        <div class="status-box">
            <h3>🎯 Modal Features</h3>
            <ul class="feature-list">
                <li><i class="fas fa-check"></i> Real-time points display</li>
                <li><i class="fas fa-check"></i> Transaction history</li>
                <li><i class="fas fa-check"></i> Available rewards catalog</li>
                <li><i class="fas fa-check"></i> Tier level display</li>
                <li><i class="fas fa-check"></i> One-click redemption</li>
                <li><i class="fas fa-check"></i> Beautiful animations</li>
                <li><i class="fas fa-check"></i> Mobile responsive</li>
            </ul>
        </div>
        
        <div class="status-box">
            <h3>📊 Test Data</h3>
            <p>The modal will load your actual points and transactions from the database.</p>
            <p><strong>Expected Points:</strong> Based on your recent orders</p>
            <p><strong>Available Rewards:</strong> ₹50, ₹100, ₹200 coupons + Free shipping</p>
        </div>
    </div>

    <!-- Include the rewards modal from rewards.php -->
    <?php
    // Get the modal HTML from rewards.php
    $rewardsContent = file_get_contents('rewards.php');
    
    // Extract just the modal part
    preg_match('/<!-- Rewards Modal -->(.*?)<!-- footer start -->/s', $rewardsContent, $matches);
    if (isset($matches[1])) {
        echo $matches[1];
    }
    
    // Extract the modal styles
    preg_match('/\/\* Rewards Modal Styles \*\/(.*?)\/\* End Modal Styles \*\//s', $rewardsContent, $styleMatches);
    if (isset($styleMatches[1])) {
        echo '<style>' . $styleMatches[1] . '</style>';
    }
    
    // Extract the modal JavaScript
    preg_match('/<script>\s*\/\/ Rewards Modal JavaScript(.*?)<\/script>/s', $rewardsContent, $jsMatches);
    if (isset($jsMatches[1])) {
        echo '<script>// Rewards Modal JavaScript' . $jsMatches[1] . '</script>';
    }
    ?>

    <!-- Fallback styles and scripts if extraction fails -->
    <style>
    /* Rewards Modal Styles */
    .rewards-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
    }

    .rewards-modal-content {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        margin: 2% auto;
        padding: 0;
        border-radius: 20px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .rewards-modal-header {
        background: linear-gradient(135deg, #ff8c00 0%, #2d5016 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 20px 20px 0 0;
        position: relative;
    }

    .rewards-modal-title {
        font-size: 1.8rem;
        font-weight: bold;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rewards-close {
        position: absolute;
        right: 20px;
        top: 20px;
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .rewards-close:hover {
        transform: scale(1.1);
        opacity: 0.8;
    }

    .rewards-modal-body {
        padding: 30px;
    }

    .rewards-points-summary {
        background: linear-gradient(135deg, #ff8c00 0%, #ffb347 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(255, 140, 0, 0.3);
    }

    .rewards-points-number {
        font-size: 3rem;
        font-weight: bold;
        margin: 10px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .rewards-points-label {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .rewards-tier-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-top: 10px;
    }

    .rewards-tabs {
        display: flex;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }

    .rewards-tab {
        flex: 1;
        padding: 15px;
        text-align: center;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }

    .rewards-tab.active {
        color: #ff8c00;
        border-bottom-color: #ff8c00;
    }

    .rewards-tab:hover {
        color: #ff8c00;
        background: rgba(255, 140, 0, 0.1);
    }

    .rewards-tab-content {
        display: none;
    }

    .rewards-tab-content.active {
        display: block;
    }

    .rewards-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .rewards-empty-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    </style>

    <script>
    // Rewards Modal JavaScript
    function openRewardsModal() {
        document.getElementById('rewardsModal').style.display = 'block';
        loadUserRewards();
    }

    function closeRewardsModal() {
        document.getElementById('rewardsModal').style.display = 'none';
    }

    function openRedeemModal() {
        document.getElementById('rewardsModal').style.display = 'block';
        showTab('redeem');
        loadUserRewards();
    }

    function showTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.rewards-tab-content');
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Remove active class from all tabs
        const tabs = document.querySelectorAll('.rewards-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        
        // Show selected tab content
        document.getElementById(tabName + '-tab').classList.add('active');
        
        // Add active class to clicked tab
        event.target.classList.add('active');
    }

    function loadUserRewards() {
        // Load user points and transactions
        fetch('get_user_rewards.php')
            .then(response => response.json())
            .then(data => {
                console.log('Rewards data:', data);
                if (data.success) {
                    // Update points display
                    document.getElementById('userPointsDisplay').textContent = data.points.total_points;
                    document.getElementById('userTierDisplay').textContent = data.points.tier_level + ' Member';
                    
                    // Load transactions
                    loadTransactions(data.transactions);
                    
                    // Load rewards catalog
                    loadRewardsCatalog(data.catalog, data.points.total_points);
                } else {
                    console.error('Failed to load rewards data:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading rewards:', error);
            });
    }

    function loadTransactions(transactions) {
        const transactionsList = document.getElementById('transactionsList');
        
        if (transactions.length === 0) {
            transactionsList.innerHTML = `
                <div class="rewards-empty-state">
                    <div class="rewards-empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <p>No transactions yet. Start shopping to earn points!</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        transactions.forEach(transaction => {
            const isEarned = transaction.transaction_type === 'earned';
            const pointsClass = isEarned ? 'earned' : 'redeemed';
            const pointsPrefix = isEarned ? '+' : '';
            
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #e9ecef; border-radius: 10px; margin-bottom: 10px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #2d5016;">${transaction.description}</h4>
                        <p style="margin: 0; color: #6c757d; font-size: 0.9rem;">${new Date(transaction.created_at).toLocaleDateString()}</p>
                    </div>
                    <div style="font-size: 1.2rem; font-weight: bold; color: ${isEarned ? '#28a745' : '#dc3545'};">
                        ${pointsPrefix}${transaction.points} points
                    </div>
                </div>
            `;
        });
        
        transactionsList.innerHTML = html;
    }

    function loadRewardsCatalog(catalog, userPoints) {
        const rewardsCatalog = document.getElementById('rewardsCatalog');
        
        if (catalog.length === 0) {
            rewardsCatalog.innerHTML = `
                <div class="rewards-empty-state">
                    <div class="rewards-empty-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <p>No rewards available at the moment.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        catalog.forEach(reward => {
            const canRedeem = userPoints >= reward.points_required;
            const buttonText = canRedeem ? 'Redeem Now' : `Need ${reward.points_required - userPoints} more points`;
            
            html += `
                <div style="border: 1px solid #e9ecef; border-radius: 15px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 style="font-size: 1.2rem; font-weight: bold; color: #2d5016; margin: 0;">${reward.reward_name}</h3>
                        <div style="background: #ff8c00; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">${reward.points_required} points</div>
                    </div>
                    <p style="color: #6c757d; margin-bottom: 15px;">
                        ${reward.reward_type === 'discount' ? '₹' + reward.reward_value + ' discount on your next order' : 
                          reward.reward_type === 'free_shipping' ? 'Free shipping on any order' : 
                          'Special reward'}
                    </p>
                    <button style="background: ${canRedeem ? 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' : '#6c757d'}; color: white; border: none; padding: 10px 20px; border-radius: 25px; font-weight: 600; cursor: ${canRedeem ? 'pointer' : 'not-allowed'};" 
                            ${!canRedeem ? 'disabled' : ''} 
                            onclick="redeemReward(${reward.id}, '${reward.reward_name}', ${reward.points_required})">
                        ${buttonText}
                    </button>
                </div>
            `;
        });
        
        rewardsCatalog.innerHTML = html;
    }

    function redeemReward(rewardId, rewardName, pointsRequired) {
        if (confirm(`Redeem "${rewardName}" for ${pointsRequired} points?`)) {
            alert('Redemption feature is working! (This would process the actual redemption)');
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('rewardsModal');
        if (event.target === modal) {
            closeRewardsModal();
        }
    }
    </script>

    <!-- Rewards Modal HTML -->
    <div id="rewardsModal" class="rewards-modal">
        <div class="rewards-modal-content">
            <div class="rewards-modal-header">
                <h2 class="rewards-modal-title">
                    <i class="fas fa-coins"></i>
                    My Rewards Dashboard
                </h2>
                <span class="rewards-close" onclick="closeRewardsModal()">&times;</span>
            </div>
            <div class="rewards-modal-body">
                <!-- Points Summary -->
                <div class="rewards-points-summary">
                    <div class="rewards-points-label">Your Current Points</div>
                    <div class="rewards-points-number" id="userPointsDisplay">0</div>
                    <div class="rewards-tier-badge" id="userTierDisplay">Bronze Member</div>
                </div>
                
                <!-- Tabs -->
                <div class="rewards-tabs">
                    <button class="rewards-tab active" onclick="showTab('transactions')">
                        <i class="fas fa-history"></i> Transactions
                    </button>
                    <button class="rewards-tab" onclick="showTab('redeem')">
                        <i class="fas fa-gift"></i> Redeem
                    </button>
                </div>
                
                <!-- Transactions Tab -->
                <div id="transactions-tab" class="rewards-tab-content active">
                    <div id="transactionsList">
                        <div class="rewards-empty-state">
                            <div class="rewards-empty-icon">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <p>Loading your transactions...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Redeem Tab -->
                <div id="redeem-tab" class="rewards-tab-content">
                    <div id="rewardsCatalog">
                        <div class="rewards-empty-state">
                            <div class="rewards-empty-icon">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <p>Loading available rewards...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
