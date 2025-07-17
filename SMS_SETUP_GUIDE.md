# 📱 Admin SMS Notification Setup Guide

## Overview
This guide will help you set up SMS notifications for admin alerts while keeping WhatsApp notifications for customers via Interakt API.

## 🚀 Quick Start

### Step 1: Get Fast2SMS API Key
1. Visit [Fast2SMS.com](https://www.fast2sms.com/)
2. Sign up for a free account
3. Get ₹50 free SMS credits
4. Go to Dashboard → API Keys
5. Copy your API key

### Step 2: Configure SMS System
1. Open `sms_api/SMSNotification.php`
2. Replace `YOUR_FAST2SMS_API_KEY` with your actual API key:
   ```php
   $this->apiKey = "your_actual_api_key_here";
   ```
3. Update admin phone number if needed (currently set to 8208593432)

### Step 3: Test SMS Integration
1. **Simple API Test**: Visit `http://localhost/nutrify/test_sms_api_simple.php`
   - Test SMS API directly
   - Verify API key authentication
   - Send test SMS to admin number
2. **Full Integration Test**: Visit `http://localhost/nutrify/test_sms_integration.php`
   - Test all SMS functions
   - Check database integration
   - Verify SMS logs are created

### Step 4: Use SMS Admin Panel
1. Visit: `http://localhost/nutrify/admin_sms_panel.php`
2. Test SMS sending
3. Monitor SMS logs

## 📋 What's Changed

### ✅ Files Updated
- **Order Processing**: All order placement files now send admin SMS + customer WhatsApp
- **Auto Processor**: `auto_order_processor.php` sends admin SMS + customer WhatsApp
- **OMS System**: Delivery automation sends admin SMS + customer WhatsApp
- **Admin Panel**: New SMS admin panel created

### ✅ SMS Notifications (Admin Only)
- **Order Placed**: Admin gets SMS when new order is placed
- **Order Shipped**: Admin gets SMS when order ships
- **Order Status**: Admin gets SMS for order updates
- **Custom SMS**: Admin can send custom SMS messages

### ✅ WhatsApp Notifications (Customer)
- **Order Confirmation**: Customer gets WhatsApp via Interakt
- **Order Shipped**: Customer gets WhatsApp with tracking
- **Order Delivered**: Customer gets WhatsApp confirmation
- **Payment Reminders**: Customer gets WhatsApp reminders

### ✅ Features
- **Free SMS API**: Uses Fast2SMS with free credits
- **Admin Panel**: Easy SMS management interface
- **Bulk SMS**: Send notifications to multiple orders
- **SMS Logs**: Track all SMS activity
- **Error Handling**: Graceful fallback if SMS fails

## 🔧 Configuration Options

### SMS API Settings
```php
// In sms_api/SMSNotification.php
$this->apiKey = "YOUR_API_KEY";           // Your Fast2SMS API key
$this->apiUrl = "https://www.fast2sms.com/dev/bulkV2";  // API endpoint
$this->adminPhone = "8208593432";         // Admin notification number
```

### Message Templates
SMS messages are automatically formatted with:
- Order details (ID, amount, customer name)
- Tracking information (for shipped orders)
- Payment links (for reminders)
- Delivery confirmations

## 📱 SMS Types (Admin Only)

### 1. Order Placed (Admin Notification)
```
🛒 NEW ORDER ALERT!
Order ID: MN001234
Customer: John Doe
Amount: ₹1,299
Payment: Online
Time: 07-Jul-2025 14:30
```

### 2. Order Shipped (Admin Notification)
```
📦 ORDER SHIPPED ALERT!
Order ID: MN001234
Customer: John Doe
Phone: 9876543210
Amount: ₹1,299
Tracking: DL123456789
Time: 07-Jul-2025 16:45
```

### 3. Custom Admin SMS
```
Custom message content as entered
in the admin panel for specific
notifications or alerts.
```

## 📱 WhatsApp Types (Customer)

### 1. Order Confirmation (via Interakt)
Uses your existing Interakt templates for customer order confirmations.

### 2. Order Shipped (via Interakt)
Uses your existing Interakt templates with tracking information.

### 3. Payment Reminders (via Interakt)
Uses your existing Interakt templates for payment follow-ups.

## 🎯 Testing

### Test SMS Integration
1. Run: `http://localhost/nutrify/test_sms_integration.php`
2. Check all tests pass
3. Verify SMS functions work

### Test Admin Panel
1. Open: `http://localhost/nutrify/admin_sms_panel.php`
2. Send test SMS to your number
3. Check SMS logs

### Test Order Flow
1. Place a test order
2. Check admin gets SMS notification
3. Update order status to shipped
4. Check customer gets SMS notification

## 📊 Monitoring

### SMS Logs
- Location: `sms_api/logs/sms_YYYY-MM-DD.log`
- Contains: All SMS activity with timestamps
- Format: `[timestamp] STATUS: message details`

### Admin Panel
- Real-time SMS sending
- Bulk operations
- Recent order management
- SMS log viewing

## 🔒 Security

### API Key Protection
- Store API key securely
- Don't commit to version control
- Use environment variables in production

### Phone Number Validation
- Automatic 10-digit validation
- Indian mobile number format
- Country code handling

## 💰 Cost Management

### Free Credits
- Fast2SMS provides ₹50 free credits
- Approximately 500-1000 SMS messages
- Monitor usage in Fast2SMS dashboard

### Rate Limiting
- Built-in delays between bulk SMS
- Prevents API rate limiting
- Ensures reliable delivery

## 🚨 Troubleshooting

### Common Issues

1. **"Invalid Authentication, Check Authorization Key"**
   - Verify API key is correctly copied from Fast2SMS dashboard
   - Check for extra spaces or characters in API key
   - Ensure API key is active in Fast2SMS account
   - Test with simple API test: `test_sms_api_simple.php`

2. **"Insufficient Balance"**
   - Add credits to your Fast2SMS account
   - Check account balance in Fast2SMS dashboard
   - Free accounts get ₹50 credits initially

3. **"Invalid Number"**
   - Ensure 10-digit Indian mobile number format
   - Remove country codes (+91)
   - Check for special characters or spaces

4. **Database Errors (PaymentMode column)**
   - Fixed in updated version
   - Database query now uses only existing columns
   - No database changes required

5. **SMS Not Received**
   - Check if SMS was sent successfully in logs
   - Verify phone number is correct
   - Check mobile network connectivity
   - Some networks may delay SMS delivery

### Error Messages
- Check `sms_api/logs/` for detailed error logs
- Use admin panel to test configuration
- Verify database connectivity

## 📞 Support

### Fast2SMS Support
- Website: https://www.fast2sms.com/
- Documentation: Available in dashboard
- Support: Contact through their website

### System Support
- Test file: `test_sms_integration.php`
- Admin panel: `admin_sms_panel.php`
- Logs: `sms_api/logs/`

## 🎉 Benefits

### Advantages of SMS over WhatsApp
- ✅ No template approval required
- ✅ Instant delivery
- ✅ Universal compatibility
- ✅ Free credits available
- ✅ Simple integration
- ✅ Reliable delivery

### Business Benefits
- 📱 Instant customer notifications
- 🔔 Admin order alerts
- 📊 Delivery tracking updates
- 💳 Payment reminders
- 📈 Better customer engagement

---

**Ready to use!** Your SMS notification system is now configured and ready to replace WhatsApp notifications.
