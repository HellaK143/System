<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entrepreneur') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$user_id = $_SESSION['user_id'];
$activity = $conn->query("SELECT activity, details, created_at FROM activity_log WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 50");
$conn->close();
$page_title = 'Activity Log';
$breadcrumb_items = ['Activity Log'];
ob_start();
?>
<div class="container my-4">
    <h3 class="mb-4">Activity Log</h3>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr><th>Activity</th><th>Details</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php if ($activity && $activity->num_rows > 0): while($a = $activity->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($a['activity']) ?></td>
                    <td><?= htmlspecialchars($a['details']) ?></td>
                    <td><?= htmlspecialchars($a['created_at']) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="3" class="text-center text-muted">No activity found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_entrepreneur.php'; 