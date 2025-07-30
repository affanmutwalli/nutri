<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI API Speed Comparison</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        .api-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .api-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .api-card.winner {
            border: 3px solid #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%);
        }
        .api-card.winner::before {
            content: "🏆 FASTEST";
            position: absolute;
            top: -10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .api-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .speed-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .speed-lightning { background: #28a745; color: white; }
        .speed-fast { background: #ffc107; color: black; }
        .speed-medium { background: #fd7e14; color: white; }
        .speed-slow { background: #dc3545; color: white; }
        .metric {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .metric-value {
            font-weight: bold;
            color: #007bff;
        }
        .setup-steps {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            width: 100%;
        }
        .test-button:hover { background: #0056b3; }
        .test-button:disabled { background: #6c757d; cursor: not-allowed; }
        .results {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .result-success { background: #d4edda; color: #155724; }
        .result-error { background: #f8d7da; color: #721c24; }
        .result-pending { background: #fff3cd; color: #856404; }
        .comparison-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .pro { color: #28a745; font-weight: bold; }
        .con { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 AI API Speed Comparison</h1>
        <p>Compare different AI APIs for your chatbot - find the fastest and most cost-effective solution</p>
    </div>

    <div class="api-grid">
        <!-- Groq API -->
        <div class="api-card winner">
            <div class="api-title">
                ⚡ Groq API
                <span class="speed-badge speed-lightning">LIGHTNING FAST</span>
            </div>
            <div class="metric">
                <span>Speed:</span>
                <span class="metric-value">500+ tokens/sec</span>
            </div>
            <div class="metric">
                <span>Cost:</span>
                <span class="metric-value">FREE</span>
            </div>
            <div class="metric">
                <span>Model:</span>
                <span class="metric-value">Llama 3.1 8B</span>
            </div>
            <div class="metric">
                <span>Rate Limit:</span>
                <span class="metric-value">30 req/min</span>
            </div>
            <div class="setup-steps">
                <strong>Setup:</strong><br>
                1. Go to <a href="https://console.groq.com" target="_blank">console.groq.com</a><br>
                2. Sign up (free)<br>
                3. Get API key<br>
                4. Replace key in chatbot_api_groq.php
            </div>
            <button class="test-button" onclick="testAPI('groq')">Test Groq API</button>
            <div id="groq-results" class="results" style="display: none;"></div>
        </div>

        <!-- Google Gemini -->
        <div class="api-card">
            <div class="api-title">
                🔥 Google Gemini Flash
                <span class="speed-badge speed-fast">VERY FAST</span>
            </div>
            <div class="metric">
                <span>Speed:</span>
                <span class="metric-value">Fast responses</span>
            </div>
            <div class="metric">
                <span>Cost:</span>
                <span class="metric-value">FREE</span>
            </div>
            <div class="metric">
                <span>Model:</span>
                <span class="metric-value">Gemini 1.5 Flash</span>
            </div>
            <div class="metric">
                <span>Rate Limit:</span>
                <span class="metric-value">15 req/min</span>
            </div>
            <div class="setup-steps">
                <strong>Setup:</strong><br>
                1. Go to <a href="https://aistudio.google.com/app/apikey" target="_blank">aistudio.google.com</a><br>
                2. Sign in with Google<br>
                3. Create API key<br>
                4. Replace key in chatbot_api_gemini.php
            </div>
            <button class="test-button" onclick="testAPI('gemini')">Test Gemini API</button>
            <div id="gemini-results" class="results" style="display: none;"></div>
        </div>

        <!-- DeepSeek (Current) -->
        <div class="api-card">
            <div class="api-title">
                🐌 DeepSeek (Current)
                <span class="speed-badge speed-slow">SLOW</span>
            </div>
            <div class="metric">
                <span>Speed:</span>
                <span class="metric-value">Slow responses</span>
            </div>
            <div class="metric">
                <span>Cost:</span>
                <span class="metric-value">Paid</span>
            </div>
            <div class="metric">
                <span>Model:</span>
                <span class="metric-value">DeepSeek Chat</span>
            </div>
            <div class="metric">
                <span>Rate Limit:</span>
                <span class="metric-value">Unknown</span>
            </div>
            <div class="setup-steps">
                <strong>Current Setup:</strong><br>
                Already configured and working<br>
                But slower than alternatives
            </div>
            <button class="test-button" onclick="testAPI('deepseek')">Test DeepSeek API</button>
            <div id="deepseek-results" class="results" style="display: none;"></div>
        </div>
    </div>

    <div class="comparison-table">
        <h3>📊 Detailed Comparison</h3>
        <table>
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Groq</th>
                    <th>Google Gemini</th>
                    <th>DeepSeek</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Speed</strong></td>
                    <td class="pro">⚡ 500+ tokens/sec</td>
                    <td class="pro">🔥 Very Fast</td>
                    <td class="con">🐌 Slow</td>
                </tr>
                <tr>
                    <td><strong>Cost</strong></td>
                    <td class="pro">✅ FREE</td>
                    <td class="pro">✅ FREE</td>
                    <td class="con">💰 Paid</td>
                </tr>
                <tr>
                    <td><strong>Rate Limits</strong></td>
                    <td>30 requests/minute</td>
                    <td>15 requests/minute</td>
                    <td>Unknown</td>
                </tr>
                <tr>
                    <td><strong>Setup Difficulty</strong></td>
                    <td class="pro">Easy</td>
                    <td class="pro">Easy</td>
                    <td>Already done</td>
                </tr>
                <tr>
                    <td><strong>Quality</strong></td>
                    <td class="pro">Excellent</td>
                    <td class="pro">Excellent</td>
                    <td>Good</td>
                </tr>
                <tr>
                    <td><strong>Reliability</strong></td>
                    <td class="pro">High</td>
                    <td class="pro">High</td>
                    <td>Medium</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        async function testAPI(apiType) {
            const resultsDiv = document.getElementById(apiType + '-results');
            const button = event.target;
            
            button.disabled = true;
            button.textContent = 'Testing...';
            resultsDiv.style.display = 'block';
            resultsDiv.className = 'results result-pending';
            resultsDiv.innerHTML = '⏳ Testing API speed...';
            
            const testMessage = 'What products do you have for immunity?';
            let apiFile = '';
            
            switch(apiType) {
                case 'groq':
                    apiFile = 'chatbot_api_groq.php';
                    break;
                case 'gemini':
                    apiFile = 'chatbot_api_gemini.php';
                    break;
                case 'deepseek':
                    apiFile = 'chatbot_api_fast.php';
                    break;
            }
            
            try {
                const startTime = Date.now();
                const response = await fetch(apiFile, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: testMessage })
                });
                const endTime = Date.now();
                const duration = endTime - startTime;
                
                if (response.ok) {
                    const data = await response.json();
                    resultsDiv.className = 'results result-success';
                    resultsDiv.innerHTML = `
                        ✅ <strong>Success!</strong><br>
                        Response time: <strong>${duration}ms</strong><br>
                        Response: ${data.response ? 'Received' : 'No response'}<br>
                        Products: ${data.products ? data.products.length : 0} items
                    `;
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
            } catch (error) {
                resultsDiv.className = 'results result-error';
                resultsDiv.innerHTML = `
                    ❌ <strong>Failed</strong><br>
                    Error: ${error.message}<br>
                    ${apiType === 'groq' || apiType === 'gemini' ? 'Make sure you have set up the API key!' : ''}
                `;
            }
            
            button.disabled = false;
            button.textContent = `Test ${apiType.charAt(0).toUpperCase() + apiType.slice(1)} API`;
        }
    </script>
</body>
</html>
