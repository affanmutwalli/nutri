# 🔧 Fast2SMS API Troubleshooting Guide

## Current Issue: "Invalid Authentication, Check Authorization Key"

### 📋 Possible Causes & Solutions

#### 1. **API Key Issues**
- **Wrong API Key**: Ensure you're using the correct API key from Fast2SMS dashboard
- **Expired/Disabled Key**: Check if your API key is still active
- **Copy-Paste Error**: Verify no extra spaces or characters

**Solution:**
1. Login to [Fast2SMS Dashboard](https://www.fast2sms.com/dashboard/dev-api)
2. Copy the API key exactly as shown
3. Update in `sms_api/SMSNotification.php`

#### 2. **Account Status Issues**
- **Account Not Verified**: Fast2SMS may require account verification
- **Insufficient Balance**: Account needs minimum balance
- **KYC Required**: Some features require KYC completion

**Solution:**
1. Check account status in Fast2SMS dashboard
2. Complete any pending verification
3. Add credits if balance is low

#### 3. **API Route Issues**
- **Wrong Route**: Using incorrect SMS route
- **Route Restrictions**: Some routes need special permissions

**Current Route:** `q` (Quick SMS - ₹5 per SMS, no DLT required)

#### 4. **Header Format Issues**
- **Case Sensitivity**: Fast2SMS uses lowercase `authorization`
- **Missing Headers**: Required headers not included

**Correct Format:**
```php
'authorization: YOUR_API_KEY'
'accept: */*'
'cache-control: no-cache'
'content-type: application/json'
```

## 🧪 Testing Steps

### Step 1: Check Account Status
Visit: `http://localhost/nutrify/test_sms_api_simple.php`
- Check wallet balance API response
- Verify account is active
- Confirm API key works

### Step 2: Verify API Key
1. Login to [Fast2SMS Dashboard](https://www.fast2sms.com/dashboard/dev-api)
2. Copy API key from "Dev API" section
3. Compare with key in your code
4. Ensure exact match (no spaces/extra characters)

### Step 3: Check Account Requirements
1. **Minimum Transaction**: Fast2SMS requires minimum ₹100 transaction
2. **KYC Status**: Check if KYC is completed
3. **Account Verification**: Ensure account is verified

## 🔍 Common Error Codes

| Code | Message | Solution |
|------|---------|----------|
| 412 | Invalid Authentication | Check API key |
| 413 | Authorization Key Disabled | Contact Fast2SMS support |
| 414 | IP Blacklisted | Check IP restrictions |
| 415 | Account Disabled | Contact Fast2SMS support |
| 416 | Insufficient Balance | Add credits to account |
| 999 | Complete minimum transaction | Make ₹100 transaction first |

## 🛠️ Alternative Solutions

### Option 1: Try Different SMS Provider
If Fast2SMS continues to fail, consider:
- **TextLocal**: Popular in India
- **MSG91**: Reliable service
- **2Factor**: Good for OTP/transactional SMS

### Option 2: Use Different Fast2SMS Route
Try OTP route instead of Quick SMS:
```php
$payload = [
    "route" => "otp",
    "variables_values" => "1234", // OTP number
    "numbers" => $phoneNumber
];
```

### Option 3: Contact Fast2SMS Support
If API key is correct but still failing:
1. Contact Fast2SMS support
2. Provide your account details
3. Ask about API access requirements

## 📞 Fast2SMS Account Setup

### Required Steps:
1. **Sign Up**: Create account at Fast2SMS.com
2. **Verify Mobile**: Verify your mobile number
3. **Add Credits**: Minimum ₹100 transaction required
4. **Get API Key**: From Dev API section
5. **Test API**: Use wallet API to test authentication

### Account Requirements:
- ✅ Mobile verification
- ✅ Minimum ₹100 wallet balance
- ✅ Active API key
- ⚠️ KYC (for some features)
- ⚠️ DLT registration (for promotional SMS)

## 🔧 Quick Fixes to Try

### Fix 1: Update API Key
```php
// In sms_api/SMSNotification.php line 21
$this->apiKey = "YOUR_EXACT_API_KEY_FROM_DASHBOARD";
```

### Fix 2: Test Wallet API First
```bash
curl -X POST https://www.fast2sms.com/dev/wallet \
  -H "authorization: YOUR_API_KEY"
```

### Fix 3: Try GET Method
```php
// Instead of POST, try GET method
$url = "https://www.fast2sms.com/dev/bulkV2?authorization=YOUR_API_KEY&route=q&message=test&numbers=8208593432";
$response = file_get_contents($url);
```

## 📱 Test Commands

### Test 1: Wallet Balance
```bash
curl -X POST "https://www.fast2sms.com/dev/wallet" \
  -H "authorization: YOUR_API_KEY"
```

### Test 2: Simple SMS
```bash
curl -X POST "https://www.fast2sms.com/dev/bulkV2" \
  -H "authorization: YOUR_API_KEY" \
  -H "content-type: application/json" \
  -d '{"route":"q","message":"Test SMS","numbers":"8208593432"}'
```

## 🎯 Next Steps

1. **Verify API Key**: Double-check from Fast2SMS dashboard
2. **Check Account**: Ensure account is active and funded
3. **Test Wallet API**: Confirm authentication works
4. **Try Simple Test**: Use `test_sms_api_simple.php`
5. **Contact Support**: If all else fails

## 📞 Support Contacts

- **Fast2SMS Support**: Available through their dashboard
- **Documentation**: https://www.fast2sms.com/docs
- **Dashboard**: https://www.fast2sms.com/dashboard

---

**Most Common Solution**: The API key needs to be copied exactly from the Fast2SMS dashboard Dev API section, and the account needs a minimum ₹100 transaction to activate API access.
