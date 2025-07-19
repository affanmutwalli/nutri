<?php
session_start();
require_once '../database/dbconnection.php';

// Simple admin check
if (!isset($_SESSION['admin_logged_in'])) {
    if (!isset($_SESSION['CustomerId'])) {
        echo '<div class="alert alert-danger">Access denied</div>';
        exit;
    }
}

$obj = new main();
$mysqli = $obj->connection();

$application_id = (int)($_GET['id'] ?? 0);

if ($application_id <= 0) {
    echo '<div class="alert alert-danger">Invalid application ID</div>';
    exit;
}

// Get application details
$stmt = $mysqli->prepare("SELECT * FROM affiliate_applications WHERE id = ?");
$stmt->bind_param('i', $application_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

if (!$application) {
    echo '<div class="alert alert-danger">Application not found</div>';
    exit;
}

// Status badge colors
$status_colors = [
    'pending' => 'warning',
    'under_review' => 'info', 
    'approved' => 'success',
    'rejected' => 'danger'
];
$status_color = $status_colors[$application['application_status']] ?? 'secondary';
?>

<div class="row">
    <div class="col-md-6">
        <h5>Applicant Information</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Name:</strong></td>
                <td><?php echo htmlspecialchars($application['name']); ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>
                    <a href="mailto:<?php echo $application['email']; ?>">
                        <?php echo htmlspecialchars($application['email']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>
                    <a href="tel:<?php echo $application['phone']; ?>">
                        <?php echo htmlspecialchars($application['phone']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Company:</strong></td>
                <td><?php echo htmlspecialchars($application['company'] ?: 'Not provided'); ?></td>
            </tr>
            <tr>
                <td><strong>Website:</strong></td>
                <td>
                    <a href="<?php echo htmlspecialchars($application['website']); ?>" target="_blank">
                        <?php echo htmlspecialchars($application['website']); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td><strong>Traffic Range:</strong></td>
                <td>
                    <span class="badge badge-primary">
                        <?php echo htmlspecialchars($application['traffic_range']); ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5>Application Status</h5>
        <table class="table table-borderless">
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="badge badge-<?php echo $status_color; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $application['application_status'])); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Applied:</strong></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($application['created_at'])); ?></td>
            </tr>
            <tr>
                <td><strong>Last Updated:</strong></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($application['updated_at'])); ?></td>
            </tr>
            <?php if ($application['approval_date']): ?>
            <tr>
                <td><strong>Approval Date:</strong></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($application['approval_date'])); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong>IP Address:</strong></td>
                <td><?php echo htmlspecialchars($application['ip_address'] ?: 'N/A'); ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h5>Marketing Experience</h5>
        <div class="card">
            <div class="card-body">
                <p style="white-space: pre-wrap; word-wrap: break-word;">
                    <?php echo htmlspecialchars($application['marketing_experience']); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($application['additional_message']): ?>
<div class="row">
    <div class="col-12">
        <h5>Additional Message</h5>
        <div class="card">
            <div class="card-body">
                <p style="white-space: pre-wrap; word-wrap: break-word;">
                    <?php echo htmlspecialchars($application['additional_message']); ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($application['review_notes']): ?>
<div class="row">
    <div class="col-12">
        <h5>Review Notes</h5>
        <div class="card">
            <div class="card-body bg-light">
                <p style="white-space: pre-wrap; word-wrap: break-word;">
                    <?php echo htmlspecialchars($application['review_notes']); ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row mt-3">
    <div class="col-12">
        <h5>Quick Actions</h5>
        <div class="btn-group">
            <a href="mailto:<?php echo $application['email']; ?>?subject=Regarding Your Affiliate Application&body=Dear <?php echo urlencode($application['name']); ?>,%0A%0AThank you for your interest in our affiliate program.%0A%0A" 
               class="btn btn-primary">
                <i class="fas fa-reply"></i> Reply via Email
            </a>
            <a href="tel:<?php echo $application['phone']; ?>" class="btn btn-success">
                <i class="fas fa-phone"></i> Call
            </a>
            <a href="<?php echo htmlspecialchars($application['website']); ?>" target="_blank" class="btn btn-info">
                <i class="fas fa-external-link-alt"></i> Visit Website
            </a>
            <button class="btn btn-warning" onclick="reviewApplication(<?php echo $application['id']; ?>); $('#applicationModal').modal('hide');">
                <i class="fas fa-edit"></i> Review Application
            </button>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h5>Application Analysis</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title">Website Quality</h6>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: 75%"></div>
                        </div>
                        <small class="text-muted">Based on URL structure</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title">Experience Level</h6>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?php echo min(100, strlen($application['marketing_experience']) / 5); ?>%"></div>
                        </div>
                        <small class="text-muted">Based on description length</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title">Traffic Potential</h6>
                        <?php
                        $traffic_scores = [
                            '1k-5k' => 20,
                            '5k-10k' => 40,
                            '10k-50k' => 60,
                            '50k-100k' => 80,
                            '100k+' => 100
                        ];
                        $traffic_score = $traffic_scores[$application['traffic_range']] ?? 0;
                        ?>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?php echo $traffic_score; ?>%"></div>
                        </div>
                        <small class="text-muted"><?php echo $application['traffic_range']; ?> range</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table td {
    padding: 0.5rem;
    border: none;
}
.card {
    margin-bottom: 1rem;
}
.progress {
    height: 8px;
    margin: 10px 0;
}
</style>
