<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=system_charts_export.csv');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$out = fopen('php://output', 'w');
// Users by role
fputcsv($out, ['Users by Role']);
$roles = ['mentor','entrepreneur','evaluator','admin'];
fputcsv($out, ['Role', 'Count']);
foreach ($roles as $role) {
    $res = $conn->query("SELECT COUNT(*) FROM users WHERE role='$role'");
    $count = $res ? $res->fetch_row()[0] : 0;
    fputcsv($out, [ucfirst($role), $count]);
}
fputcsv($out, []);
// Applications by status
fputcsv($out, ['Applications by Status']);
$statuses = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
fputcsv($out, ['Status', 'Count']);
foreach ($statuses as $status) {
    $res = $conn->query("SELECT COUNT(*) FROM applications WHERE status='$status'");
    $count = $res ? $res->fetch_row()[0] : 0;
    fputcsv($out, [$status, $count]);
}
fputcsv($out, []);
// Events by type
fputcsv($out, ['Events by Type']);
$event_types = ['workshop','training','mentoring'];
fputcsv($out, ['Type', 'Count']);
foreach ($event_types as $type) {
    $res = $conn->query("SELECT COUNT(*) FROM events WHERE event_type='$type'");
    $count = $res ? $res->fetch_row()[0] : 0;
    fputcsv($out, [ucfirst($type), $count]);
}
fclose($out);
$conn->close();
exit; 