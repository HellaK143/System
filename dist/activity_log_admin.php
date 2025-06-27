<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
$user_id = $_SESSION['user_id'];
// Fetch recent activities for this admin
$res = $conn->query("SELECT activity, activity_time, details FROM activity_log WHERE user_id=$user_id ORDER BY activity_time DESC LIMIT 50");
$conn->close();
$page_title = 'Admin Activity Log';
$breadcrumb_items = ['Activity Log'];
ob_start();
?>
<div class="container my-5">
    <h2>Activity Log</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>Activity</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res && $res->num_rows > 0): while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['activity_time']) ?></td>
                <td><?= htmlspecialchars($row['activity']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['details'])) ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="3" class="text-center text-muted">No activities found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/template_admin.php'; 