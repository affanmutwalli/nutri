<?php
if (mail('iammaddyk@gmail.com', 'Test Email', 'This is a test email.')) {
    echo "Mail sent successfully.";
} else {
    echo "Failed to send mail.";
}
?>
