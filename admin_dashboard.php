<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
secure_session_check();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Stats
$num_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$num_mentors = $conn->query("SELECT COUNT(*) FROM users WHERE role='mentor'")->fetch_row()[0];
$num_entrepreneurs = $conn->query("SELECT COUNT(*) FROM users WHERE role='entrepreneur'")->fetch_row()[0];
$num_evaluators = $conn->query("SELECT COUNT(*) FROM users WHERE role='evaluator'")->fetch_row()[0];
$num_sessions = $conn->query("SELECT COUNT(*) FROM sessions")->fetch_row()[0];
$num_bookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
$num_events = $conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0];
$num_applications = $conn->query("SELECT COUNT(*) FROM applications")->fetch_row()[0];
// Applications by status for pie chart
$status_labels = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
$status_counts = [];
foreach ($status_labels as $status) {
    $res = $conn->query("SELECT COUNT(*) FROM applications WHERE status='$status'");
    $status_counts[] = $res->fetch_row()[0];
}
// Recent applications (last 5)
$recent_applications = $conn->query("SELECT id, full_name, category, status, submitted_at FROM applications ORDER BY submitted_at DESC LIMIT 5");
// Recent activity (last 5 users)
$recent_users = $conn->query("SELECT username, role, email FROM users ORDER BY user_id DESC LIMIT 5");
$recent_sessions = $conn->query("SELECT session_type, date, time FROM sessions ORDER BY id DESC LIMIT 5");
$conn->close();
$page_title = 'Admin Dashboard';
$breadcrumb_items = [];
$additional_js = "\n<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>\n<script>\nwindow.addEventListener('DOMContentLoaded', function() {\n  var ctx = document.getElementById('adminStatsChart').getContext('2d');\n  new Chart(ctx, {\n    type: 'bar',\n    data: {\n      labels: ['Mentors', 'Entrepreneurs', 'Evaluators', 'Sessions', 'Bookings', 'Events', 'Applications'],\n      datasets: [{\n        label: 'Count',\n        data: [{$num_mentors}, {$num_entrepreneurs}, {$num_evaluators}, {$num_sessions}, {$num_bookings}, {$num_events}, {$num_applications}],\n        backgroundColor: [\n          '#007bff', '#17a2b8', '#28a745', '#ffc107', '#fd7e14', '#6610f2', '#dc3545'\n        ]\n      }]\n    },\n    options: {\n      responsive: true,\n      plugins: {\n        legend: { display: false },\n        title: { display: true, text: 'System Stats' }\n      }\n    }\n  });\n  var ctx2 = document.getElementById('appStatusPie').getContext('2d');\n  new Chart(ctx2, {\n    type: 'pie',\n    data: {\n      labels: " . json_encode($status_labels) . ",\n      datasets: [{\n        data: " . json_encode($status_counts) . ",\n        backgroundColor: ['#007bff','#ffc107','#28a745','#dc3545','#6c757d']\n      }]\n    },\n    options: {\n      responsive: true,\n      plugins: {\n        legend: { position: 'bottom' },\n        title: { display: true, text: 'Applications by Status' }\n      }\n    }\n  });\n});\n</script>\n";
ob_start();
?>
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card text-bg-primary h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-users fa-2x me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= $num_users ?></div>
            <div>Users</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-success h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-file-alt fa-2x me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= $num_applications ?></div>
            <div>Applications</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-warning h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-calendar-alt fa-2x me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= $num_events ?></div>
            <div>Events</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-info h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-book fa-2x me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= $num_bookings ?></div>
            <div>Bookings</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row mb-4">
  <div class="col-12 col-lg-6 mb-4 mb-lg-0">
    <div class="card">
      <div class="card-header bg-light fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>System Overview</div>
      <div class="card-body">
        <canvas id="adminStatsChart" height="80"></canvas>
        <div class="text-end mt-2">
          <a href="charts_admin.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-pie me-1"></i>View More Charts</a>
          <a href="export_charts.php" class="btn btn-outline-success btn-sm ms-2"><i class="fas fa-file-excel me-1"></i>Export Charts Data</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header bg-light fw-bold"><i class="fas fa-chart-pie text-success me-2"></i>Applications by Status</div>
      <div class="card-body">
        <canvas id="appStatusPie" height="80"></canvas>
      </div>
    </div>
  </div>
</div>
<div class="row g-4 mb-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold"><i class="fas fa-table text-primary me-2"></i>Recent Applications</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Submitted</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent_applications->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted">No applications found.</td></tr>
              <?php else: while($a = $recent_applications->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($a['id']) ?></td>
                  <td><?= htmlspecialchars($a['full_name']) ?></td>
                  <td><?= htmlspecialchars($a['category']) ?></td>
                  <td><span class="badge bg-info text-dark"><?= htmlspecialchars($a['status']) ?></span></td>
                  <td><?= htmlspecialchars($a['submitted_at']) ?></td>
                </tr>
              <?php endwhile; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold"><i class="fas fa-user-clock text-primary me-2"></i>Recent Users</div>
      <ul class="list-group list-group-flush">
        <?php if ($recent_users->num_rows === 0): ?>
          <li class="list-group-item text-muted">No users found.</li>
        <?php else: while($u = $recent_users->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?= htmlspecialchars($u['username']) ?> <span class="badge bg-secondary ms-2"><?= htmlspecialchars($u['role']) ?></span></span>
          <span class="text-muted small"> <?= htmlspecialchars($u['email']) ?> </span>
        </li>
        <?php endwhile; endif; ?>
      </ul>
    </div>
  </div>
</div>
<div class="row g-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header bg-light fw-bold"><i class="fas fa-calendar-check text-success me-2"></i>Recent Sessions</div>
      <ul class="list-group list-group-flush">
        <?php if ($recent_sessions->num_rows === 0): ?>
          <li class="list-group-item text-muted">No sessions found.</li>
        <?php else: while($s = $recent_sessions->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?= htmlspecialchars($s['session_type']) ?></span>
          <span class="text-muted small"> <?= htmlspecialchars($s['date']) ?> <?= htmlspecialchars($s['time']) ?> </span>
        </li>
        <?php endwhile; endif; ?>
      </ul>
    </div>
  </div>
</div>
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-light fw-bold"><i class="fas fa-link text-info me-2"></i>Quick Links</div>
      <div class="card-body d-flex flex-wrap gap-3">
        <a href="events_admin.php" class="btn btn-primary"><i class="fas fa-bullhorn me-2"></i>Manage Events</a>
        <a href="applications_admin.php" class="btn btn-info"><i class="fas fa-file-alt me-2"></i>Manage Applications</a>
        <a href="sessions_admin.php" class="btn btn-success"><i class="fas fa-calendar-alt me-2"></i>Manage Sessions</a>
        <a href="bookings_admin.php" class="btn btn-warning"><i class="fas fa-book me-2"></i>Manage Bookings</a>
        <a href="mentors_admin.php" class="btn btn-secondary"><i class="fas fa-user-tie me-2"></i>Manage Mentors</a>
        <a href="entrepreneurs_admin.php" class="btn btn-secondary"><i class="fas fa-user-graduate me-2"></i>Manage Entrepreneurs</a>
        <a href="evaluators_admin.php" class="btn btn-secondary"><i class="fas fa-user-check me-2"></i>Manage Evaluators</a>
        <a href="admin_messages.php" class="btn btn-dark"><i class="fas fa-envelope me-2"></i>Messages</a>
        <a href="notifications_admin.php" class="btn btn-dark"><i class="fas fa-bell me-2"></i>Notifications</a>
        <a href="settings_admin.php" class="btn btn-outline-dark"><i class="fas fa-cog me-2"></i>Settings</a>
        <a href="charts_admin.php" class="btn btn-outline-primary"><i class="fas fa-chart-bar me-2"></i>Charts</a>
      </div>
    </div>
  </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 