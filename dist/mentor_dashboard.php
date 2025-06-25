<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') {
    header('Location: ../login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'umic');
if ($conn->connect_error) die('DB error');
// Get mentor email from users table
$user_res = $conn->query("SELECT email FROM users WHERE user_id = $user_id");
$mentor_email = '';
if ($user_res && $u = $user_res->fetch_assoc()) $mentor_email = $u['email'];
// Get mentor_id from mentors table
$mentor_id = null;
if ($mentor_email) {
    $m_res = $conn->query("SELECT mentor_id FROM mentors WHERE email = '" . $conn->real_escape_string($mentor_email) . "'");
    if ($m_res && $m = $m_res->fetch_assoc()) $mentor_id = $m['mentor_id'];
}
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$where = $mentor_id ? "WHERE assigned_mentor = $mentor_id" : "WHERE 1=0";
if ($status_filter) $where .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
if ($category_filter) $where .= " AND category = '" . $conn->real_escape_string($category_filter) . "'";
$applications = [];
$res = $conn->query("SELECT full_name, email, category, status, MAX(submitted_at) as last_submitted, MIN(id) as first_app_id FROM applications $where GROUP BY full_name, email, category, status ORDER BY last_submitted DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $applications[] = $row;
    }
} else {
    echo '<div class="alert alert-danger">Query error: ' . htmlspecialchars($conn->error) . '</div>';
}
// Get unique statuses and categories for filter dropdowns (ignore current filter)
$status_opts = [];
$cat_opts = [];
$status_res = $mentor_id ? $conn->query("SELECT DISTINCT status FROM applications WHERE assigned_mentor = $mentor_id") : false;
if ($status_res) while ($r = $status_res->fetch_assoc()) $status_opts[] = $r['status'];
$cat_res = $mentor_id ? $conn->query("SELECT DISTINCT category FROM applications WHERE assigned_mentor = $mentor_id") : false;
if ($cat_res) while ($r = $cat_res->fetch_assoc()) $cat_opts[] = $r['category'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mentor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">My Mentees' Applications</h3>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-4">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach ($status_opts as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>" <?= $status_filter===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-select" name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($cat_opts as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $category_filter===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 text-end">
            <a href="mentor_dashboard.php" class="btn btn-secondary">Reset Filters</a>
        </div>
    </form>
    <?php if (empty($applications)): ?>
        <div class="alert alert-info">No applications assigned to you yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Last Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= htmlspecialchars($app['full_name']) ?></td>
                    <td><?= htmlspecialchars($app['email']) ?></td>
                    <td><?= htmlspecialchars($app['category']) ?></td>
                    <td><?= htmlspecialchars($app['status']) ?></td>
                    <td><?= htmlspecialchars($app['last_submitted']) ?></td>
                    <td><a href="applications.php?email=<?= urlencode($app['email']) ?>" class="btn btn-info btn-sm">View Applications</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <a href="../login.php" class="btn btn-secondary mt-4">Logout</a>
</div>
</body>
</html> 