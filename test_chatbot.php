<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Speed Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .test-container {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .result {
            margin: 10px 0;
            padding: 10px;
            border-radius: 3px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .timing {
            font-weight: bold;
            color: #007bff;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #218838;
        }
        button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Chatbot Performance Test</h1>
    <p>This page tests the chatbot API response times and functionality.</p>

    <div class="test-container">
        <h3>Quick Test</h3>
        <button onclick="testChatbot('Hello')">Test Simple Message</button>
        <button onclick="testChatbot('What products do you have for immunity?')">Test Product Query</button>
        <button onclick="testChatbot('I need help with digestion')">Test Health Query</button>
        <div id="quick-results"></div>
    </div>

    <div class="test-container">
        <h3>Stress Test</h3>
        <button onclick="runStressTest()">Run 5 Concurrent Requests</button>
        <div id="stress-results"></div>
    </div>

    <div class="test-container">
        <h3>Performance Comparison</h3>
        <button onclick="comparePerformance()">Compare Old vs New API Speed</button>
        <div id="comparison-results"></div>
    </div>

    <div class="test-container">
        <h3>Timeout Test</h3>
        <button onclick="testTimeout()">Test Timeout Handling</button>
        <div id="timeout-results"></div>
    </div>

    <script>
        async function testChatbot(message) {
            const startTime = Date.now();
            const resultsDiv = document.getElementById('quick-results');

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout (Groq is super fast)

                const response = await fetch('chatbot_api_groq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message }),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                const endTime = Date.now();
                const duration = endTime - startTime;

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                resultsDiv.innerHTML += `
                    <div class="result success">
                        <strong>✓ Success</strong><br>
                        Message: "${message}"<br>
                        <span class="timing">Response time: ${duration}ms</span><br>
                        Response: ${data.response || 'No response'}<br>
                        Products: ${data.products ? data.products.length : 0} items
                    </div>
                `;

            } catch (error) {
                const endTime = Date.now();
                const duration = endTime - startTime;

                resultsDiv.innerHTML += `
                    <div class="result error">
                        <strong>✗ Error</strong><br>
                        Message: "${message}"<br>
                        <span class="timing">Failed after: ${duration}ms</span><br>
                        Error: ${error.message}
                    </div>
                `;
            }
        }

        async function runStressTest() {
            const resultsDiv = document.getElementById('stress-results');
            resultsDiv.innerHTML = '<p>Running stress test...</p>';

            const messages = [
                'Hello',
                'What products do you have?',
                'I need immunity boost',
                'Help with digestion',
                'Skin care products'
            ];

            const startTime = Date.now();

            try {
                const promises = messages.map(msg => testChatbotQuiet(msg));
                const results = await Promise.allSettled(promises);

                const endTime = Date.now();
                const totalDuration = endTime - startTime;

                const successful = results.filter(r => r.status === 'fulfilled').length;
                const failed = results.filter(r => r.status === 'rejected').length;

                resultsDiv.innerHTML = `
                    <div class="result ${failed === 0 ? 'success' : 'error'}">
                        <strong>Stress Test Results</strong><br>
                        <span class="timing">Total time: ${totalDuration}ms</span><br>
                        Successful: ${successful}/${messages.length}<br>
                        Failed: ${failed}/${messages.length}
                    </div>
                `;

            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="result error">
                        <strong>Stress Test Failed</strong><br>
                        Error: ${error.message}
                    </div>
                `;
            }
        }

        async function testChatbotQuiet(message) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            const response = await fetch('chatbot_api_groq.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            return await response.json();
        }

        async function testTimeout() {
            const resultsDiv = document.getElementById('timeout-results');
            resultsDiv.innerHTML = '<p>Testing timeout handling...</p>';

            // This will test the timeout by making a request with a very short timeout
            try {
                const controller = new AbortController();
                setTimeout(() => controller.abort(), 1); // 1ms timeout - will definitely timeout

                const startTime = Date.now();
                await fetch('chatbot_api_groq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: 'Test timeout' }),
                    signal: controller.signal
                });

            } catch (error) {
                const endTime = Date.now();
                const duration = endTime - startTime;

                if (error.name === 'AbortError') {
                    resultsDiv.innerHTML = `
                        <div class="result success">
                            <strong>✓ Timeout Handling Works</strong><br>
                            <span class="timing">Aborted after: ${duration}ms</span><br>
                            Error type: ${error.name}
                        </div>
                    `;
                } else {
                    resultsDiv.innerHTML = `
                        <div class="result error">
                            <strong>✗ Unexpected Error</strong><br>
                            Error: ${error.message}
                        </div>
                    `;
                }
            }
        }

        async function comparePerformance() {
            const resultsDiv = document.getElementById('comparison-results');
            resultsDiv.innerHTML = '<p>Running performance comparison...</p>';

            const testMessage = 'What products do you have for immunity?';

            try {
                // Test old API
                const oldStartTime = Date.now();
                const oldController = new AbortController();
                const oldTimeoutId = setTimeout(() => oldController.abort(), 30000);

                const oldResponse = await fetch('chatbot_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage }),
                    signal: oldController.signal
                });

                clearTimeout(oldTimeoutId);
                const oldEndTime = Date.now();
                const oldDuration = oldEndTime - oldStartTime;

                // Test new API
                const newStartTime = Date.now();
                const newController = new AbortController();
                const newTimeoutId = setTimeout(() => newController.abort(), 20000);

                const newResponse = await fetch('chatbot_api_fast.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage }),
                    signal: newController.signal
                });

                clearTimeout(newTimeoutId);
                const newEndTime = Date.now();
                const newDuration = newEndTime - newStartTime;

                const improvement = ((oldDuration - newDuration) / oldDuration * 100).toFixed(1);

                resultsDiv.innerHTML = `
                    <div class="result success">
                        <strong>Performance Comparison Results</strong><br>
                        Old API (chatbot_api.php): <span class="timing">${oldDuration}ms</span><br>
                        New API (chatbot_api_fast.php): <span class="timing">${newDuration}ms</span><br>
                        <strong>Speed Improvement: ${improvement}% faster!</strong>
                    </div>
                `;

            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="result error">
                        <strong>Comparison Failed</strong><br>
                        Error: ${error.message}
                    </div>
                `;
            }
        }

        // Clear results function
        function clearResults() {
            document.getElementById('quick-results').innerHTML = '';
            document.getElementById('stress-results').innerHTML = '';
            document.getElementById('timeout-results').innerHTML = '';
            document.getElementById('comparison-results').innerHTML = '';
        }
    </script>

    <div style="margin-top: 20px;">
        <button onclick="clearResults()">Clear All Results</button>
    </div>
</body>
</html>