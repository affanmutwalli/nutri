<!DOCTYPE html>
<html>
<head>
    <title>Test Filter AJAX</title>
</head>
<body>
    <h2>Test Filter AJAX</h2>
    
    <button onclick="testDefaultFilter()">Test Default Filter</button>
    <button onclick="testNameSort()">Test Name Sort</button>
    <button onclick="testPriceFilter()">Test Price Filter</button>
    
    <div id="results" style="margin-top: 20px; padding: 20px; background: #f5f5f5; border-radius: 5px;">
        <p>Click a button to test the filter...</p>
    </div>

    <script>
        function testDefaultFilter() {
            const filterData = {
                product_type: ['combos'],
                packaging: [],
                size: [],
                availability: ['in-stock'],
                price_min: 0,
                price_max: 2000,
                sort: 'featured'
            };
            
            testFilter(filterData, 'Default Filter Test');
        }
        
        function testNameSort() {
            const filterData = {
                product_type: ['combos'],
                packaging: [],
                size: [],
                availability: ['in-stock'],
                price_min: 0,
                price_max: 2000,
                sort: 'name-asc'
            };
            
            testFilter(filterData, 'Name Sort Test');
        }
        
        function testPriceFilter() {
            const filterData = {
                product_type: ['combos'],
                packaging: [],
                size: [],
                availability: ['in-stock'],
                price_min: 100,
                price_max: 1000,
                sort: 'featured'
            };
            
            testFilter(filterData, 'Price Filter Test (₹100-₹1000)');
        }
        
        function testFilter(filterData, testName) {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '<p>Testing ' + testName + '...</p>';
            
            console.log('Testing filter:', filterData);
            
            fetch('filter_products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(filterData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Filter response:', data);
                
                let html = '<h3>' + testName + ' Results:</h3>';
                
                if (data.success) {
                    html += '<p><strong>Total Products:</strong> ' + data.total_count + '</p>';
                    
                    if (data.products && data.products.length > 0) {
                        html += '<table border="1" style="border-collapse: collapse; width: 100%; margin-top: 10px;">';
                        html += '<tr><th>ID</th><th>Name</th><th>Price</th><th>MRP</th><th>Savings</th></tr>';
                        
                        data.products.slice(0, 10).forEach(product => {
                            html += '<tr>';
                            html += '<td>' + product.ProductId + '</td>';
                            html += '<td>' + product.ProductName + '</td>';
                            html += '<td>₹' + product.lowest_price + '</td>';
                            html += '<td>₹' + product.mrp + '</td>';
                            html += '<td>₹' + product.savings + '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</table>';
                        
                        if (data.products.length > 10) {
                            html += '<p><em>Showing first 10 of ' + data.products.length + ' products</em></p>';
                        }
                    } else {
                        html += '<p style="color: orange;">No products found.</p>';
                    }
                } else {
                    html += '<p style="color: red;"><strong>Error:</strong> ' + (data.error || 'Unknown error') + '</p>';
                }
                
                resultsDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Filter error:', error);
                resultsDiv.innerHTML = '<p style="color: red;"><strong>Network Error:</strong> ' + error.message + '</p>';
            });
        }
    </script>
</body>
</html>
