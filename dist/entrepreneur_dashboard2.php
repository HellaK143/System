<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entrepreneur') {
    header('Location: ../login.php');
    exit();
}
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
// Get stats
$app_count = $conn->query("SELECT COUNT(*) FROM applications WHERE email='$email'")->fetch_row()[0];
$feedback_count = $conn->query("SELECT COUNT(*) FROM applications WHERE email='$email' AND feedback IS NOT NULL AND feedback != ''")->fetch_row()[0];
$mentor = $conn->query("SELECT assigned_mentor FROM applications WHERE email='$email' AND assigned_mentor IS NOT NULL LIMIT 1")->fetch_row()[0] ?? null;
$evaluator = $conn->query("SELECT assigned_evaluator FROM applications WHERE email='$email' AND assigned_evaluator IS NOT NULL LIMIT 1")->fetch_row()[0] ?? null;
$mentor_name = $mentor ? ($conn->query("SELECT name FROM users WHERE user_id=$mentor")->fetch_row()[0] ?? '-') : '-';
$evaluator_name = $evaluator ? ($conn->query("SELECT name FROM users WHERE user_id=$evaluator")->fetch_row()[0] ?? '-') : '-';
// Recent activity
$activity = $conn->query("SELECT activity, details, created_at FROM activity_log WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5");
$conn->close();
$page_title = 'Entrepreneur Dashboard';
$breadcrumb_items = ['Dashboard'];
ob_start();
?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">My Applications</h5>
                <p class="card-text display-6 fw-bold"><?= $app_count ?></p>
                <a href="my_applications_entrepreneur.php" class="btn btn-light btn-sm">View Applications</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Feedback Received</h5>
                <p class="card-text display-6 fw-bold"><?= $feedback_count ?></p>
                <a href="my_applications_entrepreneur.php" class="btn btn-light btn-sm">View Feedback</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Assigned Mentor</h5>
                <p class="card-text fw-bold"><?= htmlspecialchars($mentor_name) ?></p>
                <a href="my_applications_entrepreneur.php" class="btn btn-light btn-sm">View Mentor</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Assigned Evaluator</h5>
                <p class="card-text fw-bold"><?= htmlspecialchars($evaluator_name) ?></p>
                <a href="my_applications_entrepreneur.php" class="btn btn-light btn-sm">View Evaluator</a>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-dark text-white">Recent Activity</div>
            <div class="card-body">
                <?php if ($activity && $activity->num_rows > 0): ?>
                    <ul class="list-group list-group-flush">
                        <?php while($a = $activity->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($a['activity']) ?>:</strong> <?= htmlspecialchars($a['details']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($a['created_at']) ?></small>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted">No recent activity.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-dark text-white">Quick Links</div>
            <div class="card-body d-grid gap-2">
                <a href="submit_application_entrepreneur.php" class="btn btn-outline-primary">Submit New Application</a>
                <a href="my_applications_entrepreneur.php" class="btn btn-outline-info">View My Applications</a>
                <a href="messages_entrepreneur.php" class="btn btn-outline-success">Messages</a>
                <a href="notifications_entrepreneur.php" class="btn btn-outline-warning">Notifications</a>
                <a href="activity_log_entrepreneur.php" class="btn btn-outline-secondary">Activity Log</a>
                <a href="settings_entrepreneur.php" class="btn btn-outline-dark">Settings</a>
            </div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_entrepreneur.php'; 