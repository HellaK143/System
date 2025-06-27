<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=sessions_export.csv');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Type', 'Date', 'Time', 'Location', 'Status']);
$res = $conn->query("SELECT id, session_type, date, time, location, status FROM sessions ORDER BY date DESC, time DESC");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, $row);
}
fclose($out);
$conn->close();
exit; 