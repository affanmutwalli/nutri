<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
require_once '../database/dbconnection.php';

sec_session_start();

// Check if user is logged in using the CMS authentication system
if (login_check($mysqli) != true) {
    echo '<div class="alert alert-danger">Access denied</div>';
    exit;
}

$obj = new main();
$mysqli = $obj->connection();

$message_id = (int)($_GET['id'] ?? 0);

if ($message_id <= 0) {
    echo '<div class="alert alert-danger">Invalid message ID</div>';
    exit;
}

// Get message details
$stmt = $mysqli->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->bind_param('i', $message_id);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();
$stmt->close();

if (!$message) {
    echo '<div class="alert alert-danger">Message not found</div>';
    exit;
}

// Mark as read if it's new
if ($message['status'] === 'new') {
    $update_stmt = $mysqli->prepare("UPDATE contact_messages SET status = 'read', updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param('i', $message_id);
    $update_stmt->execute();
    $update_stmt->close();
    $message['status'] = 'read';
}
?>

<div class="row">
    <div class="col-md-6">
        <h5>Contact Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Name:</strong></td>
                <td><?php echo htmlspecialchars($message['name']); ?></td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>
                    <a href="tel:<?php echo $message['phone']; ?>">
                        <?php echo htmlspecialchars($message['phone']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>
                    <a href="mailto:<?php echo $message['email']; ?>">
                        <?php echo htmlspecialchars($message['email']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Subject:</strong></td>
                <td><?php echo htmlspecialchars($message['subject'] ?: 'No Subject'); ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="badge status-<?php echo $message['status']; ?>">
                        <?php echo ucfirst($message['status']); ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5>Technical Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Submitted:</strong></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($message['created_at'])); ?></td>
            </tr>
            <tr>
                <td><strong>Last Updated:</strong></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($message['updated_at'])); ?></td>
            </tr>
            <tr>
                <td><strong>IP Address:</strong></td>
                <td><?php echo htmlspecialchars($message['ip_address'] ?: 'N/A'); ?></td>
            </tr>
            <tr>
                <td><strong>User Agent:</strong></td>
                <td style="font-size: 11px; word-break: break-all;">
                    <?php echo htmlspecialchars(substr($message['user_agent'] ?: 'N/A', 0, 100)); ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h5>Message</h5>
        <div class="card">
            <div class="card-body">
                <p style="white-space: pre-wrap; word-wrap: break-word;">
                    <?php echo htmlspecialchars($message['message']); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h5>Quick Actions</h5>
        <div class="btn-group">
            <a href="mailto:<?php echo $message['email']; ?>?subject=Re: <?php echo urlencode($message['subject'] ?: 'Your Inquiry'); ?>&body=Dear <?php echo urlencode($message['name']); ?>,%0A%0AThank you for contacting us.%0A%0A" 
               class="btn btn-primary">
                <i class="fas fa-reply"></i> Reply via Email
            </a>
            <a href="tel:<?php echo $message['phone']; ?>" class="btn btn-success">
                <i class="fas fa-phone"></i> Call
            </a>
            <button class="btn btn-info" onclick="updateStatus(<?php echo $message['id']; ?>, 'replied')">
                <i class="fas fa-check"></i> Mark as Replied
            </button>
        </div>
    </div>
</div>

<script>
function updateStatus(messageId, status) {
    $.ajax({
        url: 'update_contact_status.php',
        type: 'POST',
        data: { message_id: messageId, status: status },
        success: function(response) {
            if (response.success) {
                $('#messageModal').modal('hide');
                location.reload(); // Refresh the page to show updated status
            } else {
                alert('Error updating status: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating status');
        }
    });
}
</script>

<style>
.status-new { background-color: #ffc107; color: #000; }
.status-read { background-color: #17a2b8; color: #fff; }
.status-replied { background-color: #28a745; color: #fff; }
</style>
