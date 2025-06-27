<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$user_id = $_SESSION['user_id'];
$success = $error = '';
// Handle mark as read
if (isset($_POST['mark_read'])) {
    $nid = intval($_POST['notification_id']);
    $conn->query("UPDATE notifications SET is_read=1 WHERE id=$nid AND user_id=$user_id");
}
// Handle delete
if (isset($_POST['delete'])) {
    $nid = intval($_POST['notification_id']);
    $conn->query("DELETE FROM notifications WHERE id=$nid AND user_id=$user_id");
}
// Handle delete all
if (isset($_POST['delete_all'])) {
    $conn->query("DELETE FROM notifications WHERE user_id=$user_id");
}
// Filters
$category = $_GET['category'] ?? '';
$type = $_GET['type'] ?? '';
$where = ["user_id=$user_id"];
if ($category) $where[] = "category='".$conn->real_escape_string($category)."'";
if ($type) $where[] = "type='".$conn->real_escape_string($type)."'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$notifications = $conn->query("SELECT * FROM notifications $where_sql ORDER BY created_at DESC");
// Get all categories for filter
$cat_res = $conn->query("SELECT DISTINCT category FROM notifications WHERE user_id=$user_id");
$categories = [];
while ($cat_res && $row = $cat_res->fetch_assoc()) {
    if ($row['category']) $categories[] = $row['category'];
}
$conn->close();
$page_title = 'Mentor Notifications';
$breadcrumb_items = ['Notifications'];
$additional_css = '.notification-card { border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 1.2rem; } .notification-icon { font-size: 1.5rem; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 1rem; } .notification-info { background: #e3f2fd; color: #1976d2; } .notification-warning { background: #fff3cd; color: #856404; } .notification-success { background: #e6f4ea; color: #218838; } .notification-error { background: #f8d7da; color: #721c24; }';
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Notifications</h2>
    <form class="row g-3 mb-4" method="get">
        <div class="col-md-4">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category===$cat?'selected':'' ?>><?= htmlspecialchars(ucfirst($cat)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="info" <?= $type==='info'?'selected':'' ?>>Info</option>
                <option value="warning" <?= $type==='warning'?'selected':'' ?>>Warning</option>
                <option value="success" <?= $type==='success'?'selected':'' ?>>Success</option>
                <option value="error" <?= $type==='error'?'selected':'' ?>>Error</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
    <form method="post" class="mb-3">
        <button type="submit" name="delete_all" class="btn btn-danger" onclick="return confirm('Delete all notifications?')"><i class="fas fa-trash"></i> Delete All</button>
    </form>
    <?php if ($notifications && $notifications->num_rows > 0): while($n = $notifications->fetch_assoc()): ?>
    <div class="notification-card d-flex align-items-center p-3 notification-<?= htmlspecialchars($n['type']) ?><?= !$n['is_read'] ? ' border border-2 border-primary' : '' ?>">
        <div class="notification-icon bg-white">
            <?php if ($n['type']==='info'): ?><i class="fas fa-info-circle"></i>
            <?php elseif ($n['type']==='warning'): ?><i class="fas fa-exclamation-triangle"></i>
            <?php elseif ($n['type']==='success'): ?><i class="fas fa-check-circle"></i>
            <?php elseif ($n['type']==='error'): ?><i class="fas fa-times-circle"></i>
            <?php endif; ?>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold">[<?= htmlspecialchars(ucfirst($n['category'])) ?>] <?= htmlspecialchars($n['title']) ?></div>
            <div class="small text-muted mb-1"><?= date('Y-m-d H:i', strtotime($n['created_at'])) ?> <?= !$n['is_read'] ? '<span class="badge bg-primary ms-2">Unread</span>' : '' ?></div>
            <div><?= nl2br(htmlspecialchars($n['message'])) ?></div>
        </div>
        <form method="post" class="ms-3">
            <input type="hidden" name="notification_id" value="<?= $n['id'] ?>">
            <?php if (!$n['is_read']): ?>
                <button type="submit" name="mark_read" class="btn btn-outline-success btn-sm me-2"><i class="fas fa-check"></i></button>
            <?php endif; ?>
            <button type="submit" name="delete" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this notification?')"><i class="fas fa-trash"></i></button>
        </form>
    </div>
    <?php endwhile; else: ?>
    <div class="alert alert-info">No notifications found for your filters.</div>
    <?php endif; ?>
</div>
<?php
$page_content = ob_get_clean();
include 'template_mentor.php'; 