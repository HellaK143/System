<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
// Summary stats
$total = $conn->query("SELECT COUNT(*) AS c FROM applications")->fetch_assoc()['c'];
$accepted = $conn->query("SELECT COUNT(*) AS c FROM applications WHERE status = 'Accepted'")->fetch_assoc()['c'];
$rejected = $conn->query("SELECT COUNT(*) AS c FROM applications WHERE status = 'Rejected'")->fetch_assoc()['c'];
$acceptance_rate = $total ? round($accepted/$total*100,2) : 0;
$rejection_rate = $total ? round($rejected/$total*100,2) : 0;
// Applicants table
$applicants = $conn->query("SELECT id, full_name, category, status, submitted_at FROM applications ORDER BY submitted_at DESC");
// Add filter options for campus and cohort
$campus = $_GET['campus'] ?? '';
$cohort = $_GET['cohort'] ?? '';
if ($campus) $where[] = "campus = '".$conn->real_escape_string($campus)."'";
if ($cohort) $where[] = "cohort = '".$conn->real_escape_string($cohort)."'";
$where_sql = $where ? ('WHERE '.implode(' AND ', $where)) : '';
// For filter dropdowns
$campus_opts = $conn->query("SELECT DISTINCT campus FROM applications ORDER BY campus");
$cohort_opts = $conn->query("SELECT DISTINCT cohort FROM applications ORDER BY cohort");
// Mentor breakdown
$mentor_res = $conn->query("SELECT u.username AS mentor, COUNT(a.id) AS total, SUM(a.status='Accepted') AS accepted, SUM(a.status='Rejected') AS rejected FROM applications a LEFT JOIN users u ON a.assigned_mentor = u.user_id $where_sql GROUP BY a.assigned_mentor");
$mentor_data = [];
while ($row = $mentor_res->fetch_assoc()) $mentor_data[] = $row;
// Campus breakdown
$campus_res = $conn->query("SELECT campus, COUNT(*) AS total, SUM(status='Accepted') AS accepted, SUM(status='Rejected') AS rejected FROM applications $where_sql GROUP BY campus");
$campus_data = [];
while ($row = $campus_res->fetch_assoc()) $campus_data[] = $row;
// Cohort breakdown
$cohort_res = $conn->query("SELECT cohort, COUNT(*) AS total, SUM(status='Accepted') AS accepted, SUM(status='Rejected') AS rejected FROM applications $where_sql GROUP BY cohort");
$cohort_data = [];
while ($row = $cohort_res->fetch_assoc()) $cohort_data[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="../admin_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Admin Dashboard</a>
    <h2>Applicants & Acceptance Reports</h2>
    <div class="row mb-4">
        <div class="col-md-3"><div class="card card-body text-center"><h4><?= $total ?></h4><div>Total Applicants</div></div></div>
        <div class="col-md-3"><div class="card card-body text-center"><h4><?= $accepted ?></h4><div>Accepted</div></div></div>
        <div class="col-md-3"><div class="card card-body text-center"><h4><?= $rejected ?></h4><div>Rejected</div></div></div>
        <div class="col-md-3"><div class="card card-body text-center"><h4><?= $acceptance_rate ?>%</h4><div>Acceptance Rate</div><div class="small text-muted">Rejection: <?= $rejection_rate ?>%</div></div></div>
    </div>
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-2">
            <select name="campus" class="form-select" onchange="this.form.submit()">
                <option value="">All Campuses</option>
                <?php while ($c = $campus_opts->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['campus']) ?>" <?= $campus===$c['campus']?'selected':'' ?>><?= htmlspecialchars($c['campus']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="cohort" class="form-select" onchange="this.form.submit()">
                <option value="">All Cohorts</option>
                <?php while ($c = $cohort_opts->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['cohort']) ?>" <?= $cohort===$c['cohort']?'selected':'' ?>><?= htmlspecialchars($c['cohort']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary">Apply Filters</button>
        </div>
    </form>
    <form method="post" action="export_report.php" class="mb-3">
        <button name="format" value="csv" class="btn btn-outline-primary">Export CSV</button>
        <button name="format" value="excel" class="btn btn-outline-success ms-2">Export Excel</button>
    </form>
    <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Status</th><th>Submitted</th></tr></thead>
        <tbody>
        <?php while ($a = $applicants->fetch_assoc()): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['full_name']) ?></td>
                <td><?= htmlspecialchars($a['category']) ?></td>
                <td><?= htmlspecialchars($a['status']) ?></td>
                <td><?= htmlspecialchars($a['submitted_at']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <!-- Mentor breakdown -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h5>Breakdown by Mentor</h5>
            <table class="table table-bordered table-sm">
                <thead><tr><th>Mentor</th><th>Total</th><th>Accepted</th><th>Rejected</th></tr></thead>
                <tbody>
                <?php foreach ($mentor_data as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['mentor']) ?></td>
                        <td><?= $m['total'] ?></td>
                        <td><?= $m['accepted'] ?></td>
                        <td><?= $m['rejected'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <canvas id="mentorChart"></canvas>
        </div>
    </div>
    <!-- Campus breakdown -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h5>Breakdown by Campus</h5>
            <table class="table table-bordered table-sm">
                <thead><tr><th>Campus</th><th>Total</th><th>Accepted</th><th>Rejected</th></tr></thead>
                <tbody>
                <?php foreach ($campus_data as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['campus']) ?></td>
                        <td><?= $c['total'] ?></td>
                        <td><?= $c['accepted'] ?></td>
                        <td><?= $c['rejected'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <canvas id="campusChart"></canvas>
        </div>
    </div>
    <!-- Cohort breakdown -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h5>Breakdown by Cohort</h5>
            <table class="table table-bordered table-sm">
                <thead><tr><th>Cohort</th><th>Total</th><th>Accepted</th><th>Rejected</th></tr></thead>
                <tbody>
                <?php foreach ($cohort_data as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['cohort']) ?></td>
                        <td><?= $c['total'] ?></td>
                        <td><?= $c['accepted'] ?></td>
                        <td><?= $c['rejected'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <canvas id="cohortChart"></canvas>
        </div>
    </div>
</div>
<script>
// Mentor chart
const mentorCtx = document.getElementById('mentorChart').getContext('2d');
new Chart(mentorCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($mentor_data as $m) echo "'".addslashes($m['mentor'])."',"; ?>],
        datasets: [
            {label:'Accepted',data:[<?php foreach ($mentor_data as $m) echo $m['accepted'].","; ?>],backgroundColor:'#28a745'},
            {label:'Rejected',data:[<?php foreach ($mentor_data as $m) echo $m['rejected'].","; ?>],backgroundColor:'#dc3545'}
        ]
    },
    options: {responsive:true}
});
// Campus chart
const campusCtx = document.getElementById('campusChart').getContext('2d');
new Chart(campusCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($campus_data as $c) echo "'".addslashes($c['campus'])."',"; ?>],
        datasets: [
            {label:'Accepted',data:[<?php foreach ($campus_data as $c) echo $c['accepted'].","; ?>],backgroundColor:'#28a745'},
            {label:'Rejected',data:[<?php foreach ($campus_data as $c) echo $c['rejected'].","; ?>],backgroundColor:'#dc3545'}
        ]
    },
    options: {responsive:true}
});
// Cohort chart
const cohortCtx = document.getElementById('cohortChart').getContext('2d');
new Chart(cohortCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($cohort_data as $c) echo "'".addslashes($c['cohort'])."',"; ?>],
        datasets: [
            {label:'Accepted',data:[<?php foreach ($cohort_data as $c) echo $c['accepted'].","; ?>],backgroundColor:'#28a745'},
            {label:'Rejected',data:[<?php foreach ($cohort_data as $c) echo $c['rejected'].","; ?>],backgroundColor:'#dc3545'}
        ]
    },
    options: {responsive:true}
});
</script>
</body>
</html> 