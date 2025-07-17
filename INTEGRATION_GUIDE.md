# 🔗 WhatsApp Integration Guide - How It All Works

## 👤 **How the System Gets Customer Names & Details**

### **Your Existing Database Structure:**
```sql
customer_master table:
- CustomerId (Primary Key)
- Name (Customer's full name)
- Email 
- MobileNo (Phone number)
- Address
- CreationDate
- IsActive

order_master table:
- OrderId (Primary Key)
- CustomerId (Foreign Key)
- TotalAmount
- PaymentStatus
- OrderStatus
- CreationDate
- DeliveryDate
```

### **How Customer Data Flows:**

1. **Customer Registration** → `exe_register.php`
   - Customer enters: Name, Email, Mobile
   - Data saved to `customer_master` table
   - WhatsApp OTP sent automatically

2. **Order Placement** → `rcus_place_order_online.php`
   - Order created with CustomerId
   - Customer details already in database
   - WhatsApp order confirmation sent

3. **WhatsApp Messages** → Uses database lookup
   - System queries database for customer details
   - Personalizes messages with actual names
   - Sends to registered mobile numbers

## 🤖 **When & How Messages Are Triggered**

### **Automatic Triggers (Event-Based):**

#### **1. Order Status Changes**
```php
// In your order processing files, add:
require_once 'whatsapp_api/order_status_update.php';

// When order status changes:
if ($orderStatus == 'shipped') {
    // Get customer details from database
    $customer = getCustomerByOrderId($orderId);
    
    sendOrderStatusUpdate(
        $orderId,
        $customer['Name'],
        $customer['MobileNo'],
        'shipped',
        $trackingNumber
    );
}
```

#### **2. Payment Events**
```php
// In payment processing:
if ($paymentStatus == 'failed') {
    $customer = getCustomerByOrderId($orderId);
    
    sendPaymentReminder(
        $customer['Name'],
        $customer['MobileNo'],
        $orderId,
        $orderAmount,
        $paymentLink
    );
}
```

#### **3. Time-Based Triggers (Cron Jobs)**
```bash
# Daily at 9 AM - Birthday wishes
0 9 * * * php /path/to/nutrify/whatsapp_api/automated_scheduler.php birthdays

# Every 2 hours - Cart abandonment
0 */2 * * * php /path/to/nutrify/whatsapp_api/automated_scheduler.php cart

# Daily at 6 PM - Payment reminders
0 18 * * * php /path/to/nutrify/whatsapp_api/automated_scheduler.php payments
```

## 🔧 **Integration Points in Your Existing Code**

### **1. Order Confirmation (Already Working)**
Your `exe_register.php` already sends WhatsApp OTP:
```php
// Line 94-136 in exe_register.php
$data = [
    "countryCode" => "+91",
    "phoneNumber" => $mobile_number,
    "template" => [
        "name" => "register_user",
        "bodyValues" => ["$OTP"]
    ]
];
```

### **2. Add to Order Processing**
In `rcus_place_order_online.php`, add after order creation:

```php
// After successful order creation (around line 150)
if ($orderId) {
    // Get customer details
    $customerQuery = "SELECT Name, MobileNo FROM customer_master WHERE CustomerId = ?";
    $customer = $obj->MysqliSelect1($customerQuery, ["Name", "MobileNo"], "i", [$data['CustomerId']]);
    
    if ($customer) {
        // Send order confirmation WhatsApp
        require_once '../whatsapp_api/order_status_update.php';
        
        $result = sendOrderStatusUpdate(
            $orderId,
            $customer[0]['Name'],
            $customer[0]['MobileNo'],
            'placed', // or 'confirmed'
            null
        );
    }
}
```

### **3. Add to Payment Processing**
In your payment callback/webhook:

```php
// When payment succeeds
if ($paymentStatus == 'success') {
    // Update order status
    updateOrderStatus($orderId, 'paid');
    
    // Send WhatsApp confirmation
    $customer = getCustomerByOrderId($orderId);
    sendOrderStatusUpdate($orderId, $customer['Name'], $customer['MobileNo'], 'confirmed');
}

// When payment fails
if ($paymentStatus == 'failed') {
    $customer = getCustomerByOrderId($orderId);
    sendPaymentReminder($customer['Name'], $customer['MobileNo'], $orderId, $amount, $retryLink);
}
```

## 📊 **Smart Triggering Logic**

### **Customer Preferences & Opt-out System**

```sql
-- Add to customer_master table
ALTER TABLE customer_master ADD COLUMN whatsapp_opt_in TINYINT(1) DEFAULT 1;
ALTER TABLE customer_master ADD COLUMN whatsapp_opt_out TINYINT(1) DEFAULT 0;
ALTER TABLE customer_master ADD COLUMN last_whatsapp_sent TIMESTAMP NULL;
```

### **Business Rules Engine**

```php
class WhatsAppTriggerRules {
    
    public static function shouldSendMessage($customerId, $messageType) {
        // Check opt-out status
        if (self::isOptedOut($customerId)) {
            return false;
        }
        
        // Check business hours
        if (!self::isBusinessHours() && $messageType !== 'urgent') {
            return false;
        }
        
        // Check frequency limits
        if (self::exceedsFrequencyLimit($customerId, $messageType)) {
            return false;
        }
        
        // Check duplicate prevention
        if (self::isDuplicateMessage($customerId, $messageType)) {
            return false;
        }
        
        return true;
    }
    
    private static function isOptedOut($customerId) {
        // Check database for opt-out status
        $obj = new Connection();
        $result = $obj->MysqliSelect1(
            "SELECT whatsapp_opt_out FROM customer_master WHERE CustomerId = ?",
            ["whatsapp_opt_out"],
            "i",
            [$customerId]
        );
        
        return $result[0]["whatsapp_opt_out"] ?? false;
    }
    
    private static function isBusinessHours() {
        $hour = (int)date('H');
        return ($hour >= 9 && $hour <= 21); // 9 AM to 9 PM
    }
    
    private static function exceedsFrequencyLimit($customerId, $messageType) {
        // Limit: Max 3 messages per day per customer
        $obj = new Connection();
        $today = date('Y-m-d');
        
        $result = $obj->MysqliSelect1(
            "SELECT COUNT(*) as count FROM whatsapp_message_log 
             WHERE customer_id = ? AND DATE(sent_at) = ?",
            ["count"],
            "is",
            [$customerId, $today]
        );
        
        return ($result[0]["count"] >= 3);
    }
}
```

## 🎯 **Trigger Scenarios & Examples**

### **Scenario 1: New Order**
```
Customer places order → 
System gets customer details from database →
Checks if customer opted in →
Sends "Order Confirmed" WhatsApp →
Logs message in database
```

### **Scenario 2: Payment Failure**
```
Payment fails →
System identifies customer from order →
Checks last payment reminder time →
If > 2 hours ago, sends payment reminder →
Includes retry link with discount
```

### **Scenario 3: Birthday**
```
Daily cron job runs →
Queries customers with today's birthday →
For each customer:
  - Checks opt-in status
  - Checks if birthday message sent this year
  - Sends personalized birthday wish with discount
  - Logs message
```

### **Scenario 4: Cart Abandonment**
```
Customer adds items to cart but doesn't order →
After 2 hours, system checks:
  - Is cart still active?
  - Has customer placed any order since?
  - Is customer opted in?
  - Sends cart reminder with product images
```

## 🔄 **Real-Time Integration Example**

### **Complete Order Flow with WhatsApp:**

```php
// In your order processing file
function processOrder($orderData) {
    try {
        // 1. Create order in database
        $orderId = createOrder($orderData);
        
        // 2. Get customer details
        $customer = getCustomerDetails($orderData['CustomerId']);
        
        // 3. Check if WhatsApp should be sent
        if (WhatsAppTriggerRules::shouldSendMessage($customer['CustomerId'], 'order_confirmation')) {
            
            // 4. Send WhatsApp confirmation
            $whatsappResult = ProductionWhatsAppAPI::sendOrderUpdate(
                $customer['MobileNo'],
                $customer['Name'],
                $orderId,
                'confirmed'
            );
            
            // 5. Log the result
            if ($whatsappResult['success']) {
                logActivity("WhatsApp order confirmation sent to " . $customer['Name']);
            }
        }
        
        return ['success' => true, 'order_id' => $orderId];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
```

## 📱 **Customer Experience Flow**

### **What Customer Sees:**

1. **Registration:** 
   - "Welcome to My Nutrify! Your OTP is 123456"

2. **Order Placed:**
   - "Hi John, your order #MN001234 has been confirmed! We'll update you on the progress."

3. **Order Shipped:**
   - "Hi John, great news! Your order #MN001234 has been shipped. Tracking: DL123456789"

4. **Birthday:**
   - "🎉 Happy Birthday John! Use code BIRTHDAY20 for 20% OFF. Valid for 7 days!"

5. **Cart Abandonment:**
   - "Hi John, your cart is waiting! Complete your order for Cholesterol Care and save on shipping."

## ⚙️ **Configuration & Control**

### **Admin Controls:**
- Enable/disable WhatsApp for specific message types
- Set business hours
- Configure frequency limits
- View delivery statistics
- Handle customer opt-outs

### **Customer Controls:**
- Opt-out via reply "STOP"
- Preference center on website
- Choose message types to receive

The system is **intelligent** - it knows when to send messages based on customer actions, preferences, and business rules! 🚀
