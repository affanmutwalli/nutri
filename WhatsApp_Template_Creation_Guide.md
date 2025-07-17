# 📱 WhatsApp Template Creation Guide for My Nutrify

## 🔐 Step 1: Access Interakt Dashboard

1. **Login to Interakt**: Go to [https://app.interakt.ai/login](https://app.interakt.ai/login)
2. **Navigate to Templates**: Go to **Templates** → **WhatsApp Templates** in the left sidebar
3. **Click "Create Template"** button

---

## 📋 Template Categories & Details

### 🚚 **ORDER STATUS TEMPLATES**

#### 1. **Order Shipped Template**
```
Template Name: order_shipped
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: 📦 Your Order is Shipped!

Body Text:
Hi {{1}}, 

Great news! Your order #{{2}} has been shipped and is on its way to you.

📋 Tracking Number: {{3}}
🚚 You can track your package using the tracking number above.

Thank you for choosing My Nutrify! 🌿

Footer: My Nutrify - Your Health Partner
```

#### 2. **Out for Delivery Template**
```
Template Name: out_for_delivery
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: 🚚 Out for Delivery Today!

Body Text:
Hi {{1}}, 

Your order #{{2}} is out for delivery and will reach you today ({{3}}).

Please keep your phone handy as our delivery partner may call you.

Thank you for choosing My Nutrify! 🌿

Footer: My Nutrify - Your Health Partner
```

#### 3. **Order Delivered Template**
```
Template Name: order_delivered
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: ✅ Order Delivered Successfully!

Body Text:
Hi {{1}}, 

Your order #{{2}} has been delivered successfully! 

We hope you love your products from {{3}}. 

Please share your feedback and help others discover the benefits of our natural products! 🌿

Footer: My Nutrify - Your Health Partner
```

#### 4. **Order Cancelled Template**
```
Template Name: order_cancelled
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: ❌ Order Cancelled

Body Text:
Hi {{1}}, 

Your order #{{2}} has been cancelled as requested.

{{3}}

If you have any questions, please contact our support team.

Thank you for choosing My Nutrify! 🌿

Footer: My Nutrify - Your Health Partner
```

---

### 🎂 **BIRTHDAY & ANNIVERSARY TEMPLATES**

#### 5. **Birthday Wishes Template**
```
Template Name: birthday_wishes
Category: MARKETING
Language: English (en)
Header Type: IMAGE
Header Image: Upload a birthday celebration image

Body Text:
🎉 Happy Birthday {{1}}! 🎂

On your special day, we're excited to offer you an exclusive birthday gift!

🎁 Use code {{2}} and get {{3}} OFF on your next order!

Celebrate your health journey with My Nutrify's premium Ayurvedic products.

Valid for 7 days only. Shop now and treat yourself! 🌿

Footer: {{4}} - Your Health Partner
```

#### 6. **Anniversary Wishes Template**
```
Template Name: anniversary_wishes
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: 🎊 Happy Anniversary!

Body Text:
Dear {{1}}, 

Congratulations on {{2}} wonderful years! 🎉

To celebrate this special milestone, enjoy {{4}} OFF with code {{3}} on your next order.

Thank you for being part of the My Nutrify family! 🌿

Footer: My Nutrify - Your Health Partner
```

---

### 💳 **PAYMENT TEMPLATES**

#### 7. **Payment Reminder Template**
```
Template Name: payment_reminder
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: ⏰ Payment Reminder

Body Text:
Hi {{1}}, 

Your order #{{2}} for {{3}} is awaiting payment.

⏰ Complete your payment within {{4}} to confirm your order.

Click the button below to pay now! 👇

Button Type: URL
Button Text: Pay Now
Button URL: {{5}}

Footer: My Nutrify - Your Health Partner
```

#### 8. **Failed Payment Retry Template**
```
Template Name: failed_payment_retry
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: 💳 Payment Failed - Retry Now

Body Text:
Hi {{1}}, 

Your payment for order #{{2}} ({{3}}) was unsuccessful.

Don't worry! You can retry your payment using the link below.

Button Type: URL
Button Text: Retry Payment
Button URL: {{4}}

Footer: My Nutrify - Your Health Partner
```

#### 9. **Failed Payment with Discount Template**
```
Template Name: failed_payment_with_discount
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: 💳 Payment Failed + Special Offer!

Body Text:
Hi {{1}}, 

Your payment for order #{{2}} ({{3}}) was unsuccessful.

🎁 As an apology, use code {{4}} and get {{5}} OFF when you retry payment!

Button Type: URL
Button Text: Retry with Discount
Button URL: {{6}}

Footer: My Nutrify - Your Health Partner
```

---

### 🛍️ **PRODUCT RECOMMENDATION TEMPLATES**

#### 10. **Product Recommendation Template**
```
Template Name: product_recommendation
Category: MARKETING
Language: English (en)
Header Type: IMAGE
Header Image: {{1}} (Product image URL)

Body Text:
Hi {{2}}, 

Based on your previous purchases, we think you'll love {{3}}!

💰 Special Price: {{4}}

This premium Ayurvedic product is perfect for your health journey! 🌿

Button Type: URL
Button Text: Shop Now
Button URL: {{5}}

Footer: My Nutrify - Your Health Partner
```

#### 11. **Product Recommendation with Discount Template**
```
Template Name: product_recommendation_with_discount
Category: MARKETING
Language: English (en)
Header Type: IMAGE
Header Image: {{1}} (Product image URL)

Body Text:
Hi {{2}}, 

We have a special recommendation for you: {{3}}!

💰 Price: {{4}}
🎁 Use code {{5}} and get {{6}} OFF!

Button Type: URL
Button Text: Shop with Discount
Button URL: {{7}}

Footer: My Nutrify - Your Health Partner
```

#### 12. **Combo Offer Template**
```
Template Name: combo_offer
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: 🎁 Special Combo Offer!

Body Text:
Hi {{1}}, 

Exclusive combo deal: {{2}}

💰 Regular Price: {{3}}
🎉 Combo Price: {{4}}
💸 You Save: {{5}}

Limited time offer! Grab this amazing deal now! 🌿

Button Type: URL
Button Text: Get Combo Deal
Button URL: {{6}}

Footer: My Nutrify - Your Health Partner
```

#### 13. **Reorder Reminder Template**
```
Template Name: reorder_reminder
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: 🔄 Time to Reorder?

Body Text:
Hi {{1}}, 

It's been a while since your last order! 

Your previous favorites: {{2}}

Last ordered {{3}}. Ready for a refill? 🌿

Button Type: URL
Button Text: Reorder Now
Button URL: {{4}}

Footer: My Nutrify - Your Health Partner
```

---

### ⭐ **FEEDBACK & REVIEW TEMPLATES**

#### 14. **Feedback Request Template**
```
Template Name: feedback_request
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: ⭐ How was your experience?

Body Text:
Hi {{1}}, 

We hope you're loving your {{2}} from {{3}}! 

Your feedback helps us serve you better and helps other customers make informed choices.

Could you spare 2 minutes to share your experience? 🌿

Button Type: URL
Button Text: Leave Review
Button URL: {{4}}

Footer: My Nutrify - Your Health Partner
```

#### 15. **Review Incentive Template**
```
Template Name: review_incentive
Category: MARKETING
Language: English (en)
Header Type: TEXT
Header Text: 🎁 Review & Get Rewarded!

Body Text:
Hi {{1}}, 

Share your product experience and get rewarded! 

🎁 Leave a review and get {{3}} OFF with code {{2}}

Your honest feedback helps our community! 🌿

Valid for 30 days. Review now: {{4}}

Footer: My Nutrify - Your Health Partner
```

#### 16. **Thank You for Review Template**
```
Template Name: thank_you_review
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: 🙏 Thank You for Your Review!

Body Text:
Hi {{1}}, 

Thank you for taking the time to share your experience with {{2}}! 

Your feedback is invaluable and helps our community make better health choices. 🌿

We appreciate your trust in our products!

Footer: My Nutrify - Your Health Partner
```

#### 17. **Thank You Review with Reward Template**
```
Template Name: thank_you_review_with_reward
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: 🙏 Thank You + Reward Inside!

Body Text:
Hi {{1}}, 

Thank you for your amazing review! 

🎁 As promised, here's your reward code {{3}} for {{4}} OFF your next order!

Your feedback helps our {{2}} community! 🌿

Footer: My Nutrify - Your Health Partner
```

#### 18. **Negative Feedback Followup Template**
```
Template Name: negative_feedback_followup
Category: TRANSACTIONAL
Language: English (en)
Header Type: TEXT
Header Text: 🤝 We Want to Make It Right

Body Text:
Hi {{1}}, 

We noticed you had concerns about order #{{2}}.

Your satisfaction is our priority, and we'd love to resolve this for you.

{{3}} is ready to assist you personally.

Button Type: URL
Button Text: Contact Support
Button URL: {{4}}

Footer: My Nutrify - Your Health Partner
```

---

## 🔧 **Template Creation Steps**

### For Each Template:

1. **Click "Create Template"**
2. **Fill Basic Info:**
   - Template Name (use exact names from above)
   - Category (TRANSACTIONAL/MARKETING)
   - Language: English (en)

3. **Design Template:**
   - Choose Header Type (TEXT/IMAGE/NONE)
   - Add Header content
   - Write Body text (copy from above)
   - Add Footer text
   - Add Buttons if needed (URL/PHONE/QUICK_REPLY)

4. **Variable Mapping:**
   - {{1}} = First variable
   - {{2}} = Second variable
   - etc.

5. **Submit for Approval:**
   - Click "Submit for Review"
   - Wait for WhatsApp approval (24-48 hours)

---

## ⚠️ **Important Notes**

1. **Template Approval**: All templates need WhatsApp approval before use
2. **Variable Limits**: Maximum 10 variables per template
3. **Character Limits**: 
   - Header: 60 characters
   - Body: 1024 characters
   - Footer: 60 characters
4. **Button Limits**: Maximum 3 buttons per template
5. **Image Requirements**: 
   - Format: JPG/PNG
   - Size: Max 5MB
   - Ratio: 1.91:1 recommended

---

## 🚀 **After Template Approval**

Once approved, update your PHP files with the correct template names and test using:
`http://localhost/nutrify/test_new_whatsapp_features.php`

---

## 📞 **Need Help?**

Contact Interakt Support if you face issues with template creation or approval.
