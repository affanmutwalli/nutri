<!DOCTYPE html>
<html>
<head>
    <title>Test PDF Upload</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus { border-color: #EA652D; outline: none; }
        button { background: linear-gradient(135deg, #EA652D, #ff8533); color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; }
        button:hover { background: linear-gradient(135deg, #d55a26, #e6742e); }
        .result { margin-top: 20px; padding: 15px; border-radius: 6px; }
        .success { background: #d4edda; color: #155724; border: 2px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 2px solid #f5c6cb; }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Test PDF Upload</h1>
        
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product ID:</label>
                <input type="number" name="ProductId" value="1" required>
                <small>Enter any existing product ID</small>
            </div>
            
            <div class="form-group">
                <label>Document Title:</label>
                <input type="text" name="DocumentTitle" value="Test Lab Report" required>
            </div>
            
            <div class="form-group">
                <label>Document Type:</label>
                <select name="DocumentType">
                    <option value="lab_report">Lab Report</option>
                    <option value="certificate">Certificate</option>
                    <option value="test_report">Test Report</option>
                    <option value="specification">Specification</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>PDF File:</label>
                <input type="file" name="DocumentFile" accept=".pdf" required>
                <small>Select a PDF file (max 10MB)</small>
            </div>
            
            <button type="submit">🚀 Upload Document</button>
        </form>
        
        <div id="result"></div>
    </div>
    
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');
            
            resultDiv.innerHTML = '<div class="result" style="background: #d1ecf1; color: #0c5460;">📤 Uploading document...</div>';
            
            fetch('cms/upload_product_document.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        resultDiv.innerHTML = `
                            <div class="result success">
                                ✅ ${data.message}
                                <br><small>Document ID: ${data.document_id || 'N/A'}</small>
                            </div>
                        `;
                        // Reset form
                        document.getElementById('uploadForm').reset();
                    } else {
                        resultDiv.innerHTML = `<div class="result error">❌ ${data.message}</div>`;
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    resultDiv.innerHTML = `<div class="result error">❌ Server response: ${text}</div>`;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                resultDiv.innerHTML = `<div class="result error">❌ Network error: ${error.message}</div>`;
            });
        });
    </script>
</body>
</html>
