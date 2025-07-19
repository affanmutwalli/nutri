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
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($phone) || empty($email) || empty($message)) {
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
    
    // Initialize database connection
    $obj = new main();
    $mysqli = $obj->connection();
    
    if (!$mysqli) {
        throw new Exception('Database connection failed');
    }
    
    // Create contact_messages table if it doesn't exist
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NULL,
        message TEXT NOT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        status ENUM('new', 'read', 'replied') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX idx_email (email),
        INDEX idx_phone (phone),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Contact form submissions'";
    
    $mysqli->query($createTableQuery);
    
    // Get client IP and user agent
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Insert contact message into database
    $stmt = $mysqli->prepare("
        INSERT INTO contact_messages (name, phone, email, subject, message, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $mysqli->error);
    }
    
    $stmt->bind_param('sssssss', $name, $phone, $email, $subject, $message, $ip_address, $user_agent);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save message: ' . $stmt->error);
    }
    
    $contact_id = $mysqli->insert_id;
    $stmt->close();
    
    // Send email notification (optional - you can enable this if you have email configured)
    $email_sent = false;
    
    // Uncomment below if you want to send email notifications
    /*
    try {
        $to = 'support@mynutrify.com'; // Your email
        $email_subject = 'New Contact Form Submission - ' . ($subject ?: 'General Inquiry');
        $email_message = "
        New contact form submission received:
        
        Name: $name
        Phone: $phone
        Email: $email
        Subject: $subject
        
        Message:
        $message
        
        Submitted at: " . date('Y-m-d H:i:s') . "
        IP Address: $ip_address
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
            $sms_message = "New contact inquiry from $name ($phone). Subject: " . ($subject ?: 'General') . ". Check admin panel for details.";
            sendSMSNotification('8208593432', $sms_message); // Your admin phone number
        }
    } catch (Exception $e) {
        error_log('SMS notification failed: ' . $e->getMessage());
    }
    
    $response['success'] = true;
    $response['message'] = 'Thank you for contacting us! We will get back to you soon.';
    $response['contact_id'] = $contact_id;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Contact form error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
?>
