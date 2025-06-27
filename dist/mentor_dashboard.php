<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') {
    header('Location: ../login.php');
    exit();
}
require_once '../db.php';
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');

// Robust mentor lookup
$mentor_id = null;
$mentor_res = $conn->query("SELECT mentor_id FROM mentors WHERE user_id = $user_id");
if ($mentor_res && $m = $mentor_res->fetch_assoc()) {
    $mentor_id = $m['mentor_id'];
} else {
    // Fallback: try to match by email
    $user_email = '';
    $user_res = $conn->query("SELECT email FROM users WHERE user_id = $user_id");
    if ($user_res && $u = $user_res->fetch_assoc()) {
        $user_email = strtolower(trim($u['email']));
        $mentor_res2 = $conn->query("SELECT mentor_id FROM mentors WHERE LOWER(TRIM(email)) = '" . $conn->real_escape_string($user_email) . "'");
        if ($mentor_res2 && $m2 = $mentor_res2->fetch_assoc()) {
            $mentor_id = $m2['mentor_id'];
        }
    }
}

if (!$mentor_id) {
    echo "<div class='alert alert-warning'>You are not yet registered as a mentor in the system. Please contact the administrator.</div>";
    exit;
}

// Dashboard stats
$num_mentees = 0;
$num_unread_messages = 0;
$num_assigned_apps = 0;
$mentees_res = $conn->query("SELECT COUNT(DISTINCT email) as cnt FROM applications WHERE assigned_mentor = $mentor_id");
if ($mentees_res && $mentees_row = $mentees_res->fetch_assoc()) $num_mentees = $mentees_row['cnt'];
$apps_res = $conn->query("SELECT COUNT(*) as cnt FROM applications WHERE assigned_mentor = $mentor_id");
if ($apps_res && $apps_row = $apps_res->fetch_assoc()) $num_assigned_apps = $apps_row['cnt'];
// Get mentor email for messages
$mentor_email = '';
$mentor_email_res = $conn->query("SELECT email FROM mentors WHERE mentor_id = $mentor_id");
if ($mentor_email_res && $me = $mentor_email_res->fetch_assoc()) $mentor_email = $me['email'];
$msg_res = $conn->query("SELECT COUNT(*) as cnt FROM messages WHERE recipient = '$mentor_email' AND is_read = 0");
if ($msg_res && $msg_row = $msg_res->fetch_assoc()) $num_unread_messages = $msg_row['cnt'];
// Applications table
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$where = ["assigned_mentor = $mentor_id"];
if ($status_filter) $where[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
if ($category_filter) $where[] = "category = '" . $conn->real_escape_string($category_filter) . "'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$applications = [];
$res = $conn->query("SELECT id, full_name, email, category, status, submitted_at FROM applications $where_sql ORDER BY submitted_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $applications[] = $row;
    }
}
// Get unique statuses and categories for filter dropdowns
$status_opts = [];
$cat_opts = [];
$status_res = $conn->query("SELECT DISTINCT status FROM applications $where_sql");
if ($status_res) while ($r = $status_res->fetch_assoc()) $status_opts[] = $r['status'];
$cat_res = $conn->query("SELECT DISTINCT category FROM applications $where_sql");
if ($cat_res) while ($r = $cat_res->fetch_assoc()) $cat_opts[] = $r['category'];
// Recent messages (last 3)
$recent_msgs = [];
$msgs_res = $conn->query("SELECT message, sent_at FROM messages WHERE LOWER(TRIM(recipient)) = '" . $conn->real_escape_string($mentor_email) . "' ORDER BY sent_at DESC LIMIT 3");
if ($msgs_res) while ($m = $msgs_res->fetch_assoc()) $recent_msgs[] = $m;
$conn->close();
$page_title = 'Mentor Dashboard';
$breadcrumb_items = ['Dashboard'];
$additional_js = "<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('myBarChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Assigned', 'Unread Msgs', 'Mentees'],
        datasets: [{
          label: 'Stats',
          backgroundColor: ['#007bff', '#28a745', '#17a2b8'],
          data: [{$num_assigned_apps}, {$num_unread_messages}, {$num_mentees}]
        }]
      },
      options: {scales: {y: {beginAtZero: true}}}
    });
  }
});
</script>";
ob_start();
?>
<!-- Debug: <div>Mentor user_id: <?= $user_id ?>, email: <?= $mentor_email ?></div> -->
<div class="container mt-5">
    <!-- Dashboard Widgets -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3"><i class="fas fa-users fa-2x text-primary"></i></div>
                    <div>
                        <div class="fw-bold fs-4"><?= $num_mentees ?></div>
                        <div class="text-muted">Assigned Mentees</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3"><i class="fas fa-envelope fa-2x text-success"></i></div>
                    <div>
                        <div class="fw-bold fs-4"><?= $num_unread_messages ?></div>
                        <div class="text-muted">Unread Messages</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3"><i class="fas fa-clipboard-list fa-2x text-info"></i></div>
                    <div>
                        <div class="fw-bold fs-4"><?= $num_assigned_apps ?></div>
                        <div class="text-muted">Assigned Applications</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Chart Widget -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-chart-bar text-primary"></i> Mentor Stats
                </div>
                <div class="card-body">
                    <canvas id="myBarChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Messages Widget -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-envelope-open-text text-primary"></i> Recent Messages</span>
                    <a href="messages_mentor.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_msgs)): ?>
                        <div class="text-muted">No recent messages.</div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_msgs as $msg): ?>
                                <li class="list-group-item small">
                                    <span class="text-muted">[<?= htmlspecialchars($msg['sent_at']) ?>]</span> <?= htmlspecialchars($msg['message']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Applications Table -->
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
                    <th>Date Submitted</th>
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
                    <td><?= htmlspecialchars($app['submitted_at']) ?></td>
                    <td>
                        <a href="application_view_mentor.php?id=<?= $app['id'] ?>" class="btn btn-info btn-sm">View</a>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendMessageModal<?= $app['id'] ?>"><i class="fas fa-envelope"></i> Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="sendMessageModal<?= $app['id'] ?>" tabindex="-1" aria-labelledby="sendMessageModalLabel<?= $app['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="../send_message.php">
                              <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($app['email']) ?>">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="sendMessageModalLabel<?= $app['id'] ?>">Send Message to <?= htmlspecialchars($app['full_name']) ?></h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
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
include 'template_mentor.php'; 