# Online Ordering System Testing Guide

## 🎯 **Quick Browser Test (Recommended)**

### Prerequisites:
1. ✅ User must be logged in (checkout requires authentication)
2. ✅ Products in cart
3. ✅ Razorpay test mode enabled (for safe testing)

### Step-by-Step Test:
1. **Login** to your account
2. **Add products** to cart
3. **Go to checkout** page
4. **Fill billing details** (will be pre-filled if saved)
5. **Select "Online Payment"**
6. **Click "Place Order"**
7. **Razorpay popup** should appear
8. **Use test card details** (see below)
9. **Complete payment**
10. **Verify order** in database/OMS

### 🧪 **Test Card Details (Razorpay Test Mode)**
```
Card Number: 4111 1111 1111 1111
Expiry: Any future date (e.g., 12/25)
CVV: Any 3 digits (e.g., 123)
Name: Any name
```

## 🔧 **Postman API Testing**

### Test 1: Order Creation API
```
POST: http://localhost/nutrify/exe_files/rcus_place_order_online.php
Content-Type: application/json

Body:
{
    "name": "Test Customer",
    "email": "test@example.com",
    "phone": "9876543210",
    "address": "123 Test Street",
    "landmark": "Near Test Mall",
    "pincode": "400001",
    "state": "Maharashtra",
    "city": "Mumbai",
    "final_total": 500,
    "paymentMethod": "Online",
    "CustomerId": 1,
    "products": [
        {
            "id": "1",
            "name": "Test Product",
            "code": "TP001",
            "size": "Medium",
            "quantity": "1",
            "offer_price": "500"
        }
    ]
}
```

### Expected Response:
```json
{
    "response": "S",
    "message": "Order placed successfully",
    "order_id": "ON000001",
    "transaction_id": "order_razorpay_id",
    "payment_status": "Pending",
    "amount": 500,
    "name": "Test Customer",
    "email": "test@example.com",
    "phone": "9876543210"
}
```

### Test 2: Payment Callback API
```
POST: http://localhost/nutrify/exe_files/razorpay_callback.php
Content-Type: application/json

Body:
{
    "order_db_id": "ON000001",
    "razorpay_payment_id": "pay_test123",
    "razorpay_order_id": "order_test123",
    "razorpay_signature": "test_signature"
}
```

## 🚨 **Important Notes**

### Current Configuration:
- **Live Razorpay Keys**: `rzp_live_DJ1mSUEz1DK4De`
- **⚠️ WARNING**: Using live keys means real money transactions!

### For Safe Testing:
1. **Switch to test keys** in `razorpay_config.php`
2. **Test keys format**: `rzp_test_xxxxxxxxxx`
3. **Or use small amounts** (₹1-₹10) for live testing

### Database Verification:
Check these tables after testing:
- `order_master` - Main order record
- `order_details` - Product line items

## 🔍 **Troubleshooting**

### Common Issues:
1. **"Session expired"** - User not logged in
2. **"Empty cart"** - No products in session
3. **"Razorpay error"** - API key issues
4. **"Database error"** - Connection/table issues

### Debug Steps:
1. Check browser console for JavaScript errors
2. Check PHP error logs
3. Verify database connection
4. Test with minimal order data
