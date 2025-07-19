<?php
header('Content-Type: application/json');

// Include database connection
require_once 'database/dbconnection.php';

// Response array
$response = array('success' => false, 'message' => '');

try {
    // Check if form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $traffic = trim($_POST['traffic'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($website) || empty($traffic) || empty($experience)) {
        throw new Exception('Please fill in all required fields');
    }
    
    // Name validation
    if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        throw new Exception('Name must contain only letters and spaces');
    }
    
    // Phone validation (Indian numbers)
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
        throw new Exception('Please enter a valid 10-digit mobile number');
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }
    
    // Website validation
    if (!filter_var($website, FILTER_VALIDATE_URL)) {
        throw new Exception('Please enter a valid website URL');
    }
    
    // Experience length validation
    if (strlen($experience) < 50) {
        throw new Exception('Please provide at least 50 characters describing your marketing experience');
    }
    
    // Initialize database connection
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception('Database connection failed');
    }
    
    // Create affiliate_applications table if it doesn't exist
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS affiliate_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        company VARCHAR(200) NULL,
        website VARCHAR(500) NOT NULL,
        traffic_range ENUM('1k-5k', '5k-10k', '10k-50k', '50k-100k', '100k+') NOT NULL,
        marketing_experience TEXT NOT NULL,
        additional_message TEXT NULL,
        application_status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending',
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        reviewed_by INT NULL,
        review_notes TEXT NULL,
        approval_date TIMESTAMP NULL,
        
        INDEX idx_email (email),
        INDEX idx_phone (phone),
        INDEX idx_status (application_status),
        INDEX idx_created_at (created_at),
        UNIQUE KEY unique_email_pending (email, application_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Affiliate program applications'";
    
    $mysqli->query($createTableQuery);
    
    // Check for existing pending application with same email
    $checkStmt = $mysqli->prepare("SELECT id FROM affiliate_applications WHERE email = ? AND application_status = 'pending'");
    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $existingResult = $checkStmt->get_result();
    
    if ($existingResult->num_rows > 0) {
        throw new Exception('You already have a pending affiliate application. Please wait for our review.');
    }
    $checkStmt->close();
    
    // Get client IP and user agent
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Insert affiliate application into database
    $stmt = $mysqli->prepare("
        INSERT INTO affiliate_applications 
        (name, email, phone, company, website, traffic_range, marketing_experience, additional_message, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $mysqli->error);
    }
    
    $stmt->bind_param('ssssssssss', $name, $email, $phone, $company, $website, $traffic, $experience, $message, $ip_address, $user_agent);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save application: ' . $stmt->error);
    }
    
    $application_id = $mysqli->insert_id;
    $stmt->close();
    
    // Send email notification to admin (optional)
    $email_sent = false;
    
    // Uncomment below if you want to send email notifications
    /*
    try {
        $to = 'affiliates@mynutrify.com'; // Your affiliate team email
        $email_subject = 'New Affiliate Application - ' . $name;
        $email_message = "
        New affiliate application received:
        
        Name: $name
        Email: $email
        Phone: $phone
        Company: $company
        Website: $website
        Traffic Range: $traffic
        
        Marketing Experience:
        $experience
        
        Additional Message:
        $message
        
        Submitted at: " . date('Y-m-d H:i:s') . "
        IP Address: $ip_address
        
        Please review this application in the admin panel.
        ";
        
        $headers = "From: noreply@mynutrify.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $email_sent = mail($to, $email_subject, $email_message, $headers);
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
    }
    */
    
    // Send SMS notification to admin (using your existing SMS system)
    try {
        // Check if SMS notification function exists
        if (function_exists('sendSMSNotification')) {
            $sms_message = "New affiliate application from $name ($email). Website: $website. Traffic: $traffic. Check admin panel for review.";
            sendSMSNotification('8208593432', $sms_message); // Your admin phone number
        }
    } catch (Exception $e) {
        error_log('SMS notification failed: ' . $e->getMessage());
    }
    
    // Send auto-reply email to applicant
    try {
        $applicant_subject = 'Affiliate Application Received - Nutrify';
        $applicant_message = "
        Dear $name,
        
        Thank you for your interest in joining the Nutrify Affiliate Program!
        
        We have successfully received your application and our affiliate team will review it within 24 hours. 
        
        Application Details:
        - Name: $name
        - Email: $email
        - Website: $website
        - Traffic Range: $traffic
        - Application ID: #$application_id
        
        What happens next?
        1. Our team will review your application and website
        2. We'll verify your traffic and marketing experience
        3. You'll receive an approval/feedback email within 24 hours
        4. If approved, you'll get access to your affiliate dashboard
        
        If you have any questions, please contact our affiliate team:
        Email: affiliates@mynutrify.com
        Phone: +91-9834243754
        
        Thank you for choosing Nutrify!
        
        Best regards,
        Nutrify Affiliate Team
        ";
        
        $applicant_headers = "From: affiliates@mynutrify.com\r\n";
        $applicant_headers .= "Reply-To: affiliates@mynutrify.com\r\n";
        $applicant_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Uncomment to send auto-reply
        // mail($email, $applicant_subject, $applicant_message, $applicant_headers);
    } catch (Exception $e) {
        error_log('Auto-reply email failed: ' . $e->getMessage());
    }
    
    $response['success'] = true;
    $response['message'] = 'Thank you for your application! Our affiliate team will review it and get back to you within 24 hours.';
    $response['application_id'] = $application_id;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Affiliate application error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
?>
