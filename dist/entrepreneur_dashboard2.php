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

// Get recent applications for this entrepreneur
$recent_applications = $conn->query("SELECT id, full_name, category, status, submitted_at FROM applications WHERE email='$email' ORDER BY submitted_at DESC LIMIT 5");

// Check if events table exists and get recent events
$recent_events = null;
$events_table_exists = $conn->query("SHOW TABLES LIKE 'events'")->num_rows > 0;
if ($events_table_exists) {
    $recent_events = $conn->query("SELECT title, date, location FROM events ORDER BY date DESC LIMIT 5");
    if (!$recent_events) {
        $recent_events = false; // Set to false if query fails
    }
}

// Chart data
$app_statuses = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
$app_status_counts = [];
foreach ($app_statuses as $status) {
    $res = $conn->query("SELECT COUNT(*) FROM applications WHERE email='$email' AND status='$status'");
    $app_status_counts[] = $res->fetch_row()[0];
}
$feedback_yes = $feedback_count;
$feedback_no = $app_count - $feedback_count;
$conn->close();
$page_title = 'Entrepreneur Dashboard';
$breadcrumb_items = ['Dashboard'];
$additional_js = "\n<script src='/system2/dist/js/chart.umd.min.js'></script>\n<script>\nwindow.addEventListener('DOMContentLoaded', function() {\n  function showFallback(id, msg) {\n    var c = document.getElementById(id);\n    if (c) {\n      var parent = c.parentElement;\n      var fallback = document.createElement('div');\n      fallback.className = 'text-center text-muted py-4';\n      fallback.innerText = msg;\n      parent.innerHTML = '';\n      parent.appendChild(fallback);\n    }\n  }\n  if (typeof Chart === 'undefined') {\n    showFallback('appStatusChart', 'Chart.js failed to load.');\n    showFallback('feedbackPie', 'Chart.js failed to load.');\n    console.error('Chart.js is not loaded.');\n    return;\n  }\n  var statusData = " . json_encode($app_status_counts, JSON_NUMERIC_CHECK) . ";\n  var statusLabels = " . json_encode($app_statuses) . ";\n  console.log('Application Status Data:', statusData, statusLabels);\n  var hasStatus = statusData.some(function(x){return x>0});\n  var ctx1 = document.getElementById('appStatusChart');\n  if (!hasStatus) {\n    showFallback('appStatusChart', 'No application data available.');\n    console.warn('No data for application status chart.');\n  } else if (ctx1) {\n    new Chart(ctx1.getContext('2d'), {\n      type: 'bar',\n      data: {\n        labels: statusLabels,\n        datasets: [{\n          label: 'Applications',\n          data: statusData,\n          backgroundColor: ['#007bff','#ffc107','#28a745','#dc3545','#6c757d']\n        }]\n      },\n      options: {\n        responsive: true,\n        plugins: {\n          legend: { display: false },\n          title: { display: true, text: 'Applications Overview' }\n        }\n      }\n    });\n  }\n  var feedbackData = [" . intval($feedback_yes) . ", " . intval($feedback_no) . "];\n  var feedbackLabels = ['Feedback Received', 'No Feedback'];\n  console.log('Feedback Data:', feedbackData, feedbackLabels);\n  var hasFeedback = feedbackData.some(function(x){return x>0});\n  var ctx2 = document.getElementById('feedbackPie');\n  if (!hasFeedback) {\n    showFallback('feedbackPie', 'No feedback data available.');\n    console.warn('No data for feedback pie chart.');\n  } else if (ctx2) {\n    new Chart(ctx2.getContext('2d'), {\n      type: 'pie',\n      data: {\n        labels: feedbackLabels,\n        datasets: [{\n          data: feedbackData,\n          backgroundColor: ['#28a745','#ffc107']\n        }]\n      },\n      options: {\n        responsive: true,\n        plugins: {\n          legend: { position: 'bottom' },\n          title: { display: true, text: 'Feedback Received' }\n        }\n      }\n    });\n  }\n});\n</script>\n";
ob_start();
?>
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card text-bg-primary h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-file-alt fa-2x me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= $app_count ?></div>
            <div>My Applications</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-success h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-comment-dots fa-2x me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= $feedback_count ?></div>
            <div>Feedback Received</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-info h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-user-tie fa-2x me-3"></i>
          <div>
            <div class="fs-6 fw-bold"><?= htmlspecialchars($mentor_name) ?></div>
            <div>Assigned Mentor</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-warning h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i class="fas fa-user-check fa-2x me-3"></i>
          <div>
            <div class="fs-6 fw-bold"><?= htmlspecialchars($evaluator_name) ?></div>
            <div>Assigned Evaluator</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row mb-4">
  <div class="col-12 col-lg-6 mb-4 mb-lg-0">
    <div class="card">
      <div class="card-header bg-light fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Applications Overview</div>
      <div class="card-body">
        <canvas id="appStatusChart" height="80"></canvas>
        <div class="text-end mt-2">
          <a href="my_applications_entrepreneur.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-folder-open me-1"></i>View All Applications</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header bg-light fw-bold"><i class="fas fa-chart-pie text-success me-2"></i>Feedback Received</div>
      <div class="card-body">
        <canvas id="feedbackPie" height="80"></canvas>
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
      <div class="card-header bg-light fw-bold"><i class="fas fa-calendar-alt text-success me-2"></i>Recent Events</div>
      <ul class="list-group list-group-flush">
        <?php if (!$events_table_exists): ?>
          <li class="list-group-item text-muted">Events table not available.</li>
        <?php elseif (!$recent_events || $recent_events->num_rows === 0): ?>
          <li class="list-group-item text-muted">No events found.</li>
        <?php else: while($e = $recent_events->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?= htmlspecialchars($e['title']) ?></span>
          <span class="text-muted small"> <?= htmlspecialchars($e['date']) ?> at <?= htmlspecialchars($e['location']) ?> </span>
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
        <a href="submit_application_entrepreneur.php" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Submit New Application</a>
        <a href="my_applications_entrepreneur.php" class="btn btn-info"><i class="fas fa-folder-open me-2"></i>View My Applications</a>
        <a href="messages_entrepreneur.php" class="btn btn-success"><i class="fas fa-envelope me-2"></i>Messages</a>
        <a href="notifications_entrepreneur.php" class="btn btn-warning"><i class="fas fa-bell me-2"></i>Notifications</a>
        <a href="activity_log_entrepreneur.php" class="btn btn-secondary"><i class="fas fa-list-alt me-2"></i>Activity Log</a>
        <a href="settings_entrepreneur.php" class="btn btn-dark"><i class="fas fa-user-cog me-2"></i>Settings</a>
      </div>
    </div>
  </div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_entrepreneur.php'; 