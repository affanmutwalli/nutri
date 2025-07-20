<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Rewards System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .test-section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .info { color: #3498db; }
        button { background: #ff8c00; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #2d5016; }
        pre { background: #f1f1f1; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🎁 Rewards System Test</h1>
    
    <div class="test-section">
        <h2>Test 1: API Endpoint Test</h2>
        <button onclick="testRewardsAPI()">Test Rewards Data API</button>
        <div id="api-result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 2: Session Check</h2>
        <button onclick="testSessionCheck()">Check Session Status</button>
        <div id="session-result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 3: Database Tables</h2>
        <button onclick="testDatabaseTables()">Check Database Tables</button>
        <div id="db-result"></div>
    </div>

    <div class="test-section">
        <h2>Test 4: Basic Setup</h2>
        <button onclick="window.location.href='setup_basic_rewards.php'">Run Basic Setup</button>
        <p>This will create only the essential tables needed for basic rewards functionality.</p>
    </div>
    
    <div class="test-section">
        <h2>Test 5: Rewards Modal</h2>
        <button onclick="openTestModal()">Open Rewards Modal</button>
        <p>This will test the modal functionality from the main page.</p>
    </div>
    
    <div class="test-section">
        <h2>Navigation</h2>
        <a href="index.php" style="color: #ff8c00;">← Back to Homepage</a> |
        <a href="setup_basic_rewards.php" style="color: #ff8c00;">Basic Setup</a> |
        <a href="setup_rewards_system.php" style="color: #ff8c00;">Full Setup</a> |
        <a href="login.php" style="color: #ff8c00;">Login</a>
    </div>

    <script>
        async function testRewardsAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<p class="info">Testing rewards API...</p>';
            
            try {
                const response = await fetch('exe_files/get_rewards_data.php');
                const data = await response.json();
                
                resultDiv.innerHTML = `
                    <p class="success">✅ API Response received</p>
                    <p><strong>Logged In:</strong> ${data.loggedIn}</p>
                    <p><strong>Customer Data:</strong> ${data.customer ? 'Available' : 'None'}</p>
                    <p><strong>Available Rewards:</strong> ${data.available_rewards.length}</p>
                    <p><strong>Earning Methods:</strong> ${data.earning_methods.length}</p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                resultDiv.innerHTML = `<p class="error">❌ Error: ${error.message}</p>`;
            }
        }
        
        async function testSessionCheck() {
            const resultDiv = document.getElementById('session-result');
            resultDiv.innerHTML = '<p class="info">Checking session...</p>';
            
            try {
                const response = await fetch('check_session.php');
                const data = await response.json();
                
                resultDiv.innerHTML = `
                    <p class="success">✅ Session check successful</p>
                    <p><strong>Logged In:</strong> ${data.loggedIn}</p>
                    <p><strong>Customer:</strong> ${data.customer ? data.customer.name || 'Available' : 'None'}</p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                resultDiv.innerHTML = `<p class="error">❌ Error: ${error.message}</p>`;
            }
        }
        
        async function testDatabaseTables() {
            const resultDiv = document.getElementById('db-result');
            resultDiv.innerHTML = '<p class="info">Checking database tables...</p>';

            try {
                const response = await fetch('exe_files/check_database_tables.php');
                const data = await response.json();

                let html = `<p class="${data.system_functional ? 'success' : 'error'}">
                    ${data.system_functional ? '✅' : '❌'} System Functional: ${data.system_functional}
                </p>`;

                html += '<h4>Table Status:</h4><ul>';
                for (const [table, status] of Object.entries(data.tables)) {
                    const icon = status.exists ? '✅' : (status.required ? '❌' : '⚠️');
                    html += `<li>${icon} ${table}: ${status.status}</li>`;
                }
                html += '</ul>';

                html += '<h4>Functionality Tests:</h4><ul>';
                for (const test of Object.values(data.functionality_tests)) {
                    const icon = test.status === 'PASS' ? '✅' : '❌';
                    html += `<li>${icon} ${test.test}: ${test.message}</li>`;
                }
                html += '</ul>';

                if (data.recommendations.length > 0) {
                    html += '<h4>Recommendations:</h4><ul>';
                    for (const rec of data.recommendations) {
                        html += `<li>${rec}</li>`;
                    }
                    html += '</ul>';
                }

                resultDiv.innerHTML = html;
            } catch (error) {
                resultDiv.innerHTML = `<p class="error">❌ Error: ${error.message}</p>`;
            }
        }
        
        function openTestModal() {
            // Redirect to main page and trigger modal
            window.location.href = 'index.php#rewards-test';
        }
    </script>
</body>
</html>
