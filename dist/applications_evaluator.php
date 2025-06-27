<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'evaluator') {
    header('Location: login.php');
    exit();
}
require_once '../db.php';
$evaluator_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$where = "WHERE assigned_evaluator = $evaluator_id";
if ($status_filter) $where .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
if ($category_filter) $where .= " AND category = '" . $conn->real_escape_string($category_filter) . "'";
$applications = [];
$res = $conn->query("SELECT id, full_name, category, status, submitted_at FROM applications $where ORDER BY id DESC");
while ($row = $res->fetch_assoc()) {
    // Get average score
    $avg = '-';
    $score_res = $conn->query("SELECT AVG(score) as avg_score FROM evaluation_scores WHERE application_id = " . intval($row['id']));
    if ($score_row = $score_res->fetch_assoc()) {
        $avg = $score_row['avg_score'] ? round($score_row['avg_score'],2) : '-';
    }
    $row['avg_score'] = $avg;
    $applications[] = $row;
}
// Get unique statuses and categories for filter dropdowns
$status_opts = [];
$cat_opts = [];
$res = $conn->query("SELECT DISTINCT status FROM applications WHERE assigned_evaluator = $evaluator_id");
while ($r = $res->fetch_assoc()) $status_opts[] = $r['status'];
$res = $conn->query("SELECT DISTINCT category FROM applications WHERE assigned_evaluator = $evaluator_id");
while ($r = $res->fetch_assoc()) $cat_opts[] = $r['category'];
$conn->close();
$page_title = 'My Assigned Applications';
$breadcrumb_items = ['Applications'];
ob_start();
?>
<div class="container mt-5">
    <h3 class="mb-4">My Assigned Applications</h3>
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
            <a href="applications_evaluator.php" class="btn btn-secondary">Reset Filters</a>
        </div>
    </form>
    <?php if (empty($applications)): ?>
        <div class="alert alert-info">No applications assigned to you yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Applicant Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Average Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['full_name']) ?></td>
                    <td><?= htmlspecialchars($app['category']) ?></td>
                    <td><?= htmlspecialchars($app['status']) ?></td>
                    <td><?= htmlspecialchars($app['submitted_at']) ?></td>
                    <td><?= $app['avg_score'] ?></td>
                    <td>
                        <a href="evaluate_application_evaluator.php?id=<?= $app['id'] ?>" class="btn btn-primary btn-sm">Evaluate</a>
                        <a href="application_view_evaluator.php?id=<?= $app['id'] ?>" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 