<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Test - Nutrify</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #ec6504; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        input:focus, textarea:focus { outline: none; border-color: #ec6504; box-shadow: 0 0 5px rgba(236, 101, 4, 0.3); }
        button { background: #ec6504; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #d55a04; }
        button:disabled { opacity: 0.7; cursor: not-allowed; }
        .spinner { animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .test-info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Contact Form Test</h1>
        
        <div class="test-info">
            <strong>Test Instructions:</strong>
            <ul>
                <li>Fill out the form below to test the contact functionality</li>
                <li>Form submissions will be saved to the database</li>
                <li>You can view submissions in the CMS admin panel</li>
                <li>Form includes validation for name, phone, and email</li>
            </ul>
        </div>

        <form id="contactForm" method="POST" action="process_contact.php">
            <div class="form-group">
                <label for="name">Your Name *</label>
                <input type="text" name="name" id="name" placeholder="Enter your name" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" name="phone" id="phone" placeholder="Enter your 10-digit phone number" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" placeholder="Enter your email address" required>
            </div>
            
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" placeholder="Enter subject (optional)">
            </div>
            
            <div class="form-group">
                <label for="message">Message *</label>
                <textarea name="message" id="message" rows="5" placeholder="Your message here..." required></textarea>
            </div>
            
            <button type="submit" id="submitBtn">
                <span id="btnText">Send Message</span>
                <span id="btnLoader" style="display: none;">
                    <i class="spinner">⟳</i> Sending...
                </span>
            </button>
        </form>

        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h3>Admin Panel Access:</h3>
            <p>After submitting, you can view the message in the admin panel:</p>
            <a href="cms/contact_messages.php" target="_blank" style="color: #ec6504; text-decoration: none;">
                📧 View Contact Messages in CMS
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            // Form validation
            const name = $('#name').val().trim();
            const phone = $('#phone').val().trim();
            const email = $('#email').val().trim();
            const message = $('#message').val().trim();
            
            // Name validation
            if (!/^[A-Za-z\s]+$/.test(name)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Name',
                    text: 'Name must contain only letters and spaces'
                });
                return;
            }
            
            // Phone validation (Indian numbers)
            if (!/^[6-9]\d{9}$/.test(phone)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Phone',
                    text: 'Please enter a valid 10-digit mobile number starting with 6-9'
                });
                return;
            }
            
            // Email validation
            if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address'
                });
                return;
            }
            
            // Submit form if validations pass
            submitContactForm();
        });
        
        function submitContactForm() {
            const formData = {
                name: $('#name').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                subject: $('#subject').val(),
                message: $('#message').val()
            };
            
            // Show loading state
            $('#btnText').hide();
            $('#btnLoader').show();
            $('#submitBtn').prop('disabled', true);
            
            $.ajax({
                url: 'process_contact.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Message Sent!',
                            text: 'Thank you for contacting us. We will get back to you soon.',
                            confirmButtonColor: '#ec6504'
                        });
                        $('#contactForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Something went wrong. Please try again.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                },
                complete: function() {
                    // Reset button state
                    $('#btnText').show();
                    $('#btnLoader').hide();
                    $('#submitBtn').prop('disabled', false);
                }
            });
        }
    });
    </script>
</body>
</html>
