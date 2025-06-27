<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
// Users by role
$roles = ['mentor','entrepreneur','evaluator','admin'];
$user_counts = [];
foreach ($roles as $role) {
    $res = $conn->query("SELECT COUNT(*) FROM users WHERE role='$role'");
    $user_counts[] = $res->fetch_row()[0];
}
// Applications by status
$statuses = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
$app_counts = [];
foreach ($statuses as $status) {
    $res = $conn->query("SELECT COUNT(*) FROM applications WHERE status='$status'");
    $app_counts[] = $res->fetch_row()[0];
}
// Events by type
$event_types = ['workshop','training','mentoring'];
$event_counts = [];
foreach ($event_types as $type) {
    $res = $conn->query("SELECT COUNT(*) FROM events WHERE event_type='$type'");
    $event_counts[] = $res->fetch_row()[0];
}
$conn->close();
$page_title = 'Admin Charts';
$breadcrumb_items = ['Charts'];
$additional_js = "\n<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>\n<script>\nwindow.addEventListener('DOMContentLoaded', function() {\n  new Chart(document.getElementById('usersByRole'), {\n    type: 'pie',\n    data: {\n      labels: ['Mentor','Entrepreneur','Evaluator','Admin'],\n      datasets: [{\n        data: [".implode(',',$user_counts)."],\n        backgroundColor: ['#007bff','#17a2b8','#28a745','#6c757d']\n      }]\n    },\n    options: {plugins: {title: {display:true, text:'Users by Role'}}}\n  });\n  new Chart(document.getElementById('appsByStatus'), {\n    type: 'bar',\n    data: {\n      labels: ['Submitted','Under Review','Shortlisted','Rejected','Accepted'],\n      datasets: [{\n        label: 'Applications',\n        data: [".implode(',',$app_counts)."],\n        backgroundColor: ['#007bff','#ffc107','#28a745','#dc3545','#6c757d']\n      }]\n    },\n    options: {plugins: {title: {display:true, text:'Applications by Status'}}}\n  });\n  new Chart(document.getElementById('eventsByType'), {\n    type: 'doughnut',\n    data: {\n      labels: ['Workshop','Training','Mentoring'],\n      datasets: [{\n        data: [".implode(',',$event_counts)."],\n        backgroundColor: ['#6610f2','#fd7e14','#20c997']\n      }]\n    },\n    options: {plugins: {title: {display:true, text:'Events by Type'}}}\n  });\n});\n</script>\n";
ob_start();
?>
<div class="container my-5">
  <h2 class="mb-4">System Charts</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-light fw-bold"><i class="fas fa-users text-primary me-2"></i>Users by Role</div>
        <div class="card-body">
          <canvas id="usersByRole" height="200"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-light fw-bold"><i class="fas fa-file-alt text-info me-2"></i>Applications by Status</div>
        <div class="card-body">
          <canvas id="appsByStatus" height="200"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-light fw-bold"><i class="fas fa-bullhorn text-success me-2"></i>Events by Type</div>
        <div class="card-body">
          <canvas id="eventsByType" height="200"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$page_content = ob_get_clean();
if (isset($additional_js)) echo $additional_js;
include __DIR__ . '/dist/template_admin.php'; 