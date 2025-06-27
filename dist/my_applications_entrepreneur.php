<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entrepreneur') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$email = $_SESSION['email'];
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$where = "WHERE email = '" . $conn->real_escape_string($email) . "'";
if ($status_filter) $where .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
if ($category_filter) $where .= " AND category = '" . $conn->real_escape_string($category_filter) . "'";
$applications = [];
$res = $conn->query("SELECT id, category, status, submitted_at, assigned_evaluator, assigned_mentor, feedback FROM applications $where ORDER BY id DESC");
while ($row = $res->fetch_assoc()) {
    $ev_name = '-';
    $mn_name = '-';
    if ($row['assigned_evaluator']) {
        $ev_res = $conn->query("SELECT name FROM users WHERE user_id = " . intval($row['assigned_evaluator']));
        if ($ev_row = $ev_res->fetch_assoc()) $ev_name = $ev_row['name'];
    }
    if ($row['assigned_mentor']) {
        $mn_res = $conn->query("SELECT name FROM users WHERE user_id = " . intval($row['assigned_mentor']));
        if ($mn_row = $mn_res->fetch_assoc()) $mn_name = $mn_row['name'];
    }
    $row['evaluator_name'] = $ev_name;
    $row['mentor_name'] = $mn_name;
    $applications[] = $row;
}
$status_opts = [];
$cat_opts = [];
$res = $conn->query("SELECT DISTINCT status FROM applications WHERE email = '" . $conn->real_escape_string($email) . "'");
while ($r = $res->fetch_assoc()) $status_opts[] = $r['status'];
$res = $conn->query("SELECT DISTINCT category FROM applications WHERE email = '" . $conn->real_escape_string($email) . "'");
while ($r = $res->fetch_assoc()) $cat_opts[] = $r['category'];
$conn->close();
$page_title = 'My Applications';
$breadcrumb_items = ['My Applications'];
ob_start();
?>
<div class="container my-4">
    <h3 class="mb-4">My Applications</h3>
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
            <a href="my_applications_entrepreneur.php" class="btn btn-secondary">Reset Filters</a>
        </div>
    </form>
    <?php if (empty($applications)): ?>
        <div class="alert alert-info">You have not submitted any applications yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Assigned Evaluator</th>
                    <th>Assigned Mentor</th>
                    <th>Feedback</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['category']) ?></td>
                    <td><?= htmlspecialchars($app['status']) ?></td>
                    <td><?= htmlspecialchars($app['submitted_at']) ?></td>
                    <td><?= htmlspecialchars($app['evaluator_name']) ?></td>
                    <td><?= htmlspecialchars($app['mentor_name']) ?></td>
                    <td><?= $app['feedback'] ? nl2br(htmlspecialchars($app['feedback'])) : '-' ?></td>
                    <td><a href="application_view_entrepreneur.php?id=<?= $app['id'] ?>" class="btn btn-info btn-sm">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <a href="submit_application_entrepreneur.php" class="btn btn-primary mt-4">Submit New Application</a>
</div>
<?php
$page_content = ob_get_clean();
include 'template_entrepreneur.php'; 