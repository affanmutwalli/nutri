<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Delhivery Order Integration</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <section class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Delhivery Order Integration</h2>
      
      <!-- Create Order Section -->
      <div class="bg-white p-6 rounded-xl shadow-md mb-10">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4">Create Order</h3>
        <form id="createOrderForm" class="grid grid-cols-1 gap-4">
          <input type="text" name="order" placeholder="Order ID" class="p-3 border rounded" required>
          <input type="text" name="customer_name" placeholder="Customer Name" class="p-3 border rounded" required>
          <input type="text" name="phone" placeholder="Customer Phone" class="p-3 border rounded" required>
          <input type="text" name="address" placeholder="Customer Address" class="p-3 border rounded" required>
          <input type="text" name="city" placeholder="City" class="p-3 border rounded" required>
          <input type="text" name="pin" placeholder="PIN Code" class="p-3 border rounded" required>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold p-3 rounded">
            Create Order
          </button>
        </form>
        <div id="createOrderResponse" class="mt-4"></div>
      </div>
      
      <!-- Track Order Section -->
      <div class="bg-white p-6 rounded-xl shadow-md">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4">Track Order</h3>
        <form id="trackForm" class="flex flex-col sm:flex-row items-center gap-4">
          <input type="text" name="order_id" placeholder="Enter AWB Number" class="w-full p-3 border rounded" required>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold p-3 rounded">
            Track Order
          </button>
        </form>
        <!-- Div to display the raw JSON response from Delhivery API -->
        <div id="trackResponse" class="mt-8">
          <!-- The raw API response will be printed here -->
        </div>
      </div>
    </div>
  </section>

  <script>
    // Handle Create Order Form submission
    document.getElementById('createOrderForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const responseDiv = document.getElementById('createOrderResponse');
      try {
        const response = await fetch('track_order.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();
        responseDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
      } catch (error) {
        responseDiv.textContent = "Error creating order.";
      }
    });

    // Handle Track Order Form submission
    document.getElementById('trackForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const orderId = e.target.order_id.value.trim();
      if (!orderId) return;
      try {
        const response = await fetch(`track_order.php?order_id=${encodeURIComponent(orderId)}`);
        const data = await response.json();
        // Display the entire raw API response in a preformatted block
        document.getElementById('trackResponse').innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
      } catch (error) {
        alert("Error fetching tracking details.");
      }
    });
  </script>
</body>
</html>
