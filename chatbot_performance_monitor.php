<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Performance Monitor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .performance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .performance-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .old-api {
            border-left: 5px solid #dc3545;
        }
        .new-api {
            border-left: 5px solid #28a745;
        }
        .metric {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .metric-value {
            font-weight: bold;
            color: #007bff;
        }
        .improvement {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .test-button:hover {
            background: #0056b3;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        .optimization-list {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .optimization-list h3 {
            color: #28a745;
            margin-top: 0;
        }
        .optimization-list ul {
            list-style-type: none;
            padding: 0;
        }
        .optimization-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .optimization-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 Chatbot Performance Monitor</h1>
        <p>Real-time performance comparison between old and optimized chatbot APIs</p>
        <button class="test-button" onclick="runPerformanceTest()">Run Performance Test</button>
        <button class="test-button" onclick="runStressTest()">Run Stress Test</button>
    </div>

    <div id="loading" class="loading" style="display: none;">
        <p>Testing chatbot performance... Please wait.</p>
    </div>

    <div id="results" style="display: none;">
        <div class="performance-grid">
            <div class="performance-card old-api">
                <h3>🐌 Old API (chatbot_api.php)</h3>
                <div class="metric">
                    <span>Response Time:</span>
                    <span class="metric-value" id="old-response-time">-</span>
                </div>
                <div class="metric">
                    <span>System Prompt Size:</span>
                    <span class="metric-value">~2,500 tokens</span>
                </div>
                <div class="metric">
                    <span>Timeout Setting:</span>
                    <span class="metric-value">30 seconds</span>
                </div>
                <div class="metric">
                    <span>Caching:</span>
                    <span class="metric-value">None</span>
                </div>
            </div>

            <div class="performance-card new-api">
                <h3>⚡ New API (chatbot_api_fast.php)</h3>
                <div class="metric">
                    <span>Response Time:</span>
                    <span class="metric-value" id="new-response-time">-</span>
                </div>
                <div class="metric">
                    <span>System Prompt Size:</span>
                    <span class="metric-value">~750 tokens (70% reduction)</span>
                </div>
                <div class="metric">
                    <span>Timeout Setting:</span>
                    <span class="metric-value">15 seconds</span>
                </div>
                <div class="metric">
                    <span>Caching:</span>
                    <span class="metric-value">Session-based</span>
                </div>
            </div>
        </div>

        <div class="improvement" id="improvement">
            Speed improvement will be calculated after testing...
        </div>
    </div>

    <div class="optimization-list">
        <h3>🔧 Performance Optimizations Applied</h3>
        <ul>
            <li>Compressed system prompt by 70% (2,500 → 750 tokens)</li>
            <li>Reduced API timeout from 30s to 15s</li>
            <li>Reduced frontend timeout from 35s to 20s</li>
            <li>Added session-based response caching</li>
            <li>Enabled HTTP/2 for faster connections</li>
            <li>Limited AI response length (max_tokens: 300)</li>
            <li>Reduced AI temperature for faster processing (0.3)</li>
            <li>Optimized cURL settings for speed</li>
            <li>Added aggressive connection timeouts (5s)</li>
            <li>Implemented cache size limits to prevent memory issues</li>
        </ul>
    </div>

    <script>
        async function runPerformanceTest() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('results').style.display = 'none';
            
            const testMessage = 'What products do you have for immunity?';
            
            try {
                // Test old API
                const oldStartTime = Date.now();
                const oldResponse = await fetch('chatbot_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage })
                });
                const oldEndTime = Date.now();
                const oldDuration = oldEndTime - oldStartTime;
                
                // Test new Groq API
                const newStartTime = Date.now();
                const newResponse = await fetch('chatbot_api_groq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage })
                });
                const newEndTime = Date.now();
                const newDuration = newEndTime - newStartTime;
                
                // Calculate improvement
                const improvement = ((oldDuration - newDuration) / oldDuration * 100).toFixed(1);
                
                // Update UI
                document.getElementById('old-response-time').textContent = oldDuration + 'ms';
                document.getElementById('new-response-time').textContent = newDuration + 'ms';
                document.getElementById('improvement').innerHTML = `
                    🎉 <strong>${improvement}% FASTER!</strong><br>
                    Saved ${oldDuration - newDuration}ms per request
                `;
                
                document.getElementById('loading').style.display = 'none';
                document.getElementById('results').style.display = 'block';
                
            } catch (error) {
                document.getElementById('loading').innerHTML = `
                    <p style="color: red;">Error during testing: ${error.message}</p>
                `;
            }
        }
        
        async function runStressTest() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading').innerHTML = '<p>Running stress test with 10 concurrent requests...</p>';
            
            const messages = [
                'Hello', 'Immunity products', 'Digestion help', 'Skin care',
                'Diabetes management', 'Women health', 'Blood pressure',
                'Thyroid support', 'Weight loss', 'Energy boost'
            ];
            
            try {
                // Test old API
                const oldStartTime = Date.now();
                const oldPromises = messages.map(msg => 
                    fetch('chatbot_api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: msg })
                    })
                );
                await Promise.all(oldPromises);
                const oldEndTime = Date.now();
                const oldDuration = oldEndTime - oldStartTime;
                
                // Test new Groq API
                const newStartTime = Date.now();
                const newPromises = messages.map(msg =>
                    fetch('chatbot_api_groq.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: msg })
                    })
                );
                await Promise.all(newPromises);
                const newEndTime = Date.now();
                const newDuration = newEndTime - newStartTime;
                
                // Calculate improvement
                const improvement = ((oldDuration - newDuration) / oldDuration * 100).toFixed(1);
                
                // Update UI
                document.getElementById('old-response-time').textContent = oldDuration + 'ms (10 requests)';
                document.getElementById('new-response-time').textContent = newDuration + 'ms (10 requests)';
                document.getElementById('improvement').innerHTML = `
                    🚀 <strong>${improvement}% FASTER!</strong> under load<br>
                    Saved ${oldDuration - newDuration}ms total (${((oldDuration - newDuration)/10).toFixed(0)}ms per request)
                `;
                
                document.getElementById('loading').style.display = 'none';
                document.getElementById('results').style.display = 'block';
                
            } catch (error) {
                document.getElementById('loading').innerHTML = `
                    <p style="color: red;">Error during stress testing: ${error.message}</p>
                `;
            }
        }
    </script>
</body>
</html>
