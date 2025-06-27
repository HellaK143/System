<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'evaluator') {
    header('Location: ../login.php');
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

// --- Summary Stats ---
function query_or_die($conn, $sql) {
    $res = $conn->query($sql);
    if ($res === false) {
        die('SQL error: ' . $conn->error . '<br>Query: ' . htmlspecialchars($sql));
    }
    return $res;
}
// Assigned Applications
$num_assigned = query_or_die($conn, "SELECT COUNT(*) FROM applications WHERE assigned_evaluator = $evaluator_id")->fetch_row()[0];
// Applications Evaluated (distinct application_id)
$num_evaluated = query_or_die($conn, "SELECT COUNT(DISTINCT application_id) FROM evaluation_scores WHERE evaluator_id = $evaluator_id")->fetch_row()[0];
// Average Score Given
$avg_score_row = query_or_die($conn, "SELECT AVG(score) FROM evaluation_scores WHERE evaluator_id = $evaluator_id")->fetch_row();
$avg_score = $avg_score_row[0] !== null ? round($avg_score_row[0],2) : '-';
// Unread Notifications
$num_unread_notif = query_or_die($conn, "SELECT COUNT(*) FROM notifications WHERE user_id = $evaluator_id AND is_read = 0")->fetch_row()[0];

// --- Applications by Status (for chart) ---
$status_labels = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
$status_counts = [];
foreach ($status_labels as $status) {
    $res = $conn->query("SELECT COUNT(*) FROM applications WHERE assigned_evaluator = $evaluator_id AND status='$status'");
    $status_counts[] = $res->fetch_row()[0];
}

// --- Scores Distribution (for chart) ---
$score_buckets = ['1-2','2-3','3-4','4-5'];
$bucket_counts = [0,0,0,0];
$score_res = $conn->query("SELECT application_id, AVG(score) as avg_score FROM evaluation_scores WHERE evaluator_id = $evaluator_id GROUP BY application_id");
while ($row = $score_res->fetch_assoc()) {
    $avg = floatval($row['avg_score']);
    if ($avg >= 1 && $avg < 2) $bucket_counts[0]++;
    elseif ($avg >= 2 && $avg < 3) $bucket_counts[1]++;
    elseif ($avg >= 3 && $avg < 4) $bucket_counts[2]++;
    elseif ($avg >= 4 && $avg <= 5) $bucket_counts[3]++;
}

// --- Recent Activity ---
$recent_activity = [];
$act_res = $conn->query("SELECT activity, activity_time, details FROM activity_log WHERE user_id=$evaluator_id ORDER BY activity_time DESC LIMIT 7");
if ($act_res) while($row = $act_res->fetch_assoc()) $recent_activity[] = $row;

// --- Recent Notifications ---
$recent_notif = [];
$notif_res = $conn->query("SELECT * FROM notifications WHERE user_id=$evaluator_id ORDER BY created_at DESC LIMIT 3");
if ($notif_res) while($row = $notif_res->fetch_assoc()) $recent_notif[] = $row;

$conn->close();
$page_title = "Evaluator Dashboard";
$breadcrumb_items = ["Dashboard"];
$additional_js = "\n<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>\n<script>\nwindow.addEventListener('DOMContentLoaded', function() {\n  var ctx = document.getElementById('appsByStatusChart').getContext('2d');\n  new Chart(ctx, {\n    type: 'pie',\n    data: {\n      labels: " . json_encode($status_labels) . ",\n      datasets: [{\n        data: " . json_encode($status_counts) . ",\n        backgroundColor: ['#007bff','#ffc107','#28a745','#dc3545','#6c757d']\n      }]\n    },\n    options: {\n      responsive: true,\n      plugins: {\n        legend: { position: 'bottom' },\n        title: { display: true, text: 'Applications by Status' }\n      }\n    }\n  });\n  var ctx2 = document.getElementById('scoresDistChart').getContext('2d');\n  new Chart(ctx2, {\n    type: 'bar',\n    data: {\n      labels: " . json_encode($score_buckets) . ",\n      datasets: [{\n        label: 'Applications',\n        data: " . json_encode($bucket_counts) . ",\n        backgroundColor: ['#17a2b8','#ffc107','#28a745','#007bff']\n      }]\n    },\n    options: {\n      responsive: true,\n      plugins: {\n        legend: { display: false },\n        title: { display: true, text: 'Scores Distribution (Avg per Application)' }\n      },\n      scales: { y: { beginAtZero: true, stepSize: 1 } }\n    }\n  });\n});\n</script>\n";
ob_start();
?>
<div class="container mt-5">
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clipboard-list fa-2x me-3"></i>
                        <div>
                            <div class="fs-4 fw-bold"><?= $num_assigned ?></div>
                            <div>Assigned Applications</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <div class="fs-4 fw-bold"><?= $num_evaluated ?></div>
                            <div>Applications Evaluated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star fa-2x me-3"></i>
                        <div>
                            <div class="fs-4 fw-bold"><?= $avg_score ?></div>
                            <div>Average Score Given</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bell fa-2x me-3"></i>
                        <div>
                            <div class="fs-4 fw-bold"><?= $num_unread_notif ?></div>
                            <div>Unread Notifications</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card">
                <div class="card-header bg-light fw-bold"><i class="fas fa-chart-pie text-success me-2"></i>Applications by Status</div>
                <div class="card-body">
                    <canvas id="appsByStatusChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light fw-bold"><i class="fas fa-chart-bar text-info me-2"></i>Scores Distribution</div>
                <div class="card-body">
                    <canvas id="scoresDistChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Activity & Notifications Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold"><i class="fas fa-list-alt text-primary me-2"></i>Recent Activity</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Activity</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_activity)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">No recent activity found.</td></tr>
                                <?php else: foreach($recent_activity as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['activity_time']) ?></td>
                                        <td><?= htmlspecialchars($row['activity']) ?></td>
                                        <td><?= nl2br(htmlspecialchars($row['details'])) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bell text-warning me-2"></i>Recent Notifications</span>
                    <a href="notifications_evaluator.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_notif)): ?>
                        <div class="alert alert-info">No notifications found.</div>
                    <?php else: foreach($recent_notif as $n): ?>
                        <div class="notification-card d-flex align-items-center p-3 notification-<?= htmlspecialchars($n['type']) ?><?= !$n['is_read'] ? ' border border-2 border-primary' : '' ?> mb-3">
                            <div class="notification-icon bg-white me-3">
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
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
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
            <a href="evaluator_dashboard.php" class="btn btn-secondary">Reset Filters</a>
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
    <a href="logout.php" class="btn btn-secondary mt-4">Logout</a>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 