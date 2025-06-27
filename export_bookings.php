<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=bookings_export.csv');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Resource', 'User', 'Start', 'End', 'Status']);
$res = $conn->query("SELECT id, resource_id, user_id, start_datetime, end_datetime, status FROM bookings ORDER BY start_datetime DESC");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, $row);
}
fclose($out);
$conn->close();
exit; 