<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groq Speed Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .speed-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .api-card {
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        .groq-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-color: #28a745;
        }
        .deepseek-card {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            border-color: #dc3545;
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .test-button:hover { background: #0056b3; }
        .test-button:disabled { background: #6c757d; cursor: not-allowed; }
        .results {
            margin-top: 20px;
            padding: 20px;
            border-radius: 5px;
            font-size: 16px;
        }
        .result-success { background: #d4edda; color: #155724; }
        .result-error { background: #f8d7da; color: #721c24; }
        .result-pending { background: #fff3cd; color: #856404; }
        .speed-metric {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .improvement {
            background: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚡ Groq vs DeepSeek Speed Test</h1>
        <p>Test the lightning-fast Groq API against your current DeepSeek setup</p>
        
        <button class="test-button" onclick="runSpeedComparison()">🚀 Run Speed Test</button>
        <button class="test-button" onclick="testGroqOnly()">⚡ Test Groq Only</button>
        
        <div id="results" class="results" style="display: none;"></div>
        
        <div class="speed-comparison">
            <div class="api-card groq-card">
                <h3>⚡ Groq API</h3>
                <div class="speed-metric" id="groq-time">-</div>
                <p>Lightning Fast • Free • Llama 4 Scout</p>
            </div>
            <div class="api-card deepseek-card">
                <h3>🐌 DeepSeek API</h3>
                <div class="speed-metric" id="deepseek-time">-</div>
                <p>Current Setup • Paid • Slower</p>
            </div>
        </div>
        
        <div id="improvement" class="improvement" style="display: none;"></div>
        
        <div style="margin-top: 30px; text-align: left;">
            <h3>🔥 Why Groq is Faster:</h3>
            <ul>
                <li><strong>Custom Hardware:</strong> Purpose-built LPU (Language Processing Unit)</li>
                <li><strong>Optimized Models:</strong> Llama 4 Scout 17B optimized for speed</li>
                <li><strong>Low Latency:</strong> 500+ tokens per second</li>
                <li><strong>Free Tier:</strong> No cost for your usage level</li>
                <li><strong>Better Infrastructure:</strong> Designed for real-time applications</li>
            </ul>
        </div>
    </div>

    <script>
        async function testGroqOnly() {
            const resultsDiv = document.getElementById('results');
            resultsDiv.style.display = 'block';
            resultsDiv.className = 'results result-pending';
            resultsDiv.innerHTML = '⏳ Testing Groq API speed...';
            
            const testMessage = 'What products do you have for immunity?';
            
            try {
                const startTime = Date.now();
                const response = await fetch('chatbot_api_groq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage })
                });
                const endTime = Date.now();
                const duration = endTime - startTime;
                
                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('groq-time').textContent = duration + 'ms';
                    resultsDiv.className = 'results result-success';
                    resultsDiv.innerHTML = `
                        ✅ <strong>Groq API Test Successful!</strong><br>
                        Response time: <strong>${duration}ms</strong><br>
                        Response received: ${data.response ? 'Yes' : 'No'}<br>
                        Products recommended: ${data.products ? data.products.length : 0}
                    `;
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
            } catch (error) {
                resultsDiv.className = 'results result-error';
                resultsDiv.innerHTML = `
                    ❌ <strong>Groq API Test Failed</strong><br>
                    Error: ${error.message}<br>
                    Make sure the API key is correctly set in chatbot_api_groq.php
                `;
            }
        }
        
        async function runSpeedComparison() {
            const resultsDiv = document.getElementById('results');
            const improvementDiv = document.getElementById('improvement');
            
            resultsDiv.style.display = 'block';
            resultsDiv.className = 'results result-pending';
            resultsDiv.innerHTML = '⏳ Running speed comparison between Groq and DeepSeek...';
            
            const testMessage = 'What products do you have for immunity?';
            
            try {
                // Test DeepSeek first
                const deepseekStartTime = Date.now();
                const deepseekResponse = await fetch('chatbot_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage })
                });
                const deepseekEndTime = Date.now();
                const deepseekDuration = deepseekEndTime - deepseekStartTime;
                
                // Test Groq
                const groqStartTime = Date.now();
                const groqResponse = await fetch('chatbot_api_groq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage })
                });
                const groqEndTime = Date.now();
                const groqDuration = groqEndTime - groqStartTime;
                
                // Calculate improvement
                const improvement = ((deepseekDuration - groqDuration) / deepseekDuration * 100).toFixed(1);
                const speedMultiplier = (deepseekDuration / groqDuration).toFixed(1);
                
                // Update UI
                document.getElementById('deepseek-time').textContent = deepseekDuration + 'ms';
                document.getElementById('groq-time').textContent = groqDuration + 'ms';
                
                if (groqResponse.ok && deepseekResponse.ok) {
                    resultsDiv.className = 'results result-success';
                    resultsDiv.innerHTML = `
                        ✅ <strong>Speed Comparison Complete!</strong><br>
                        DeepSeek: ${deepseekDuration}ms<br>
                        Groq: ${groqDuration}ms<br>
                        <strong>Groq is ${speedMultiplier}x faster!</strong>
                    `;
                    
                    improvementDiv.style.display = 'block';
                    improvementDiv.innerHTML = `
                        🚀 <strong>${improvement}% SPEED IMPROVEMENT!</strong><br>
                        Groq saves ${deepseekDuration - groqDuration}ms per request
                    `;
                } else {
                    throw new Error('One or both APIs failed');
                }
                
            } catch (error) {
                resultsDiv.className = 'results result-error';
                resultsDiv.innerHTML = `
                    ❌ <strong>Comparison Failed</strong><br>
                    Error: ${error.message}
                `;
            }
        }
    </script>
</body>
</html>
