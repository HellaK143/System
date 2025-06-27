<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=entrepreneurs_export.csv');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Course', 'Year']);
$res = $conn->query("SELECT entrepreneur_id, first_name, last_name, email, phone, course, year_of_study FROM entrepreneur ORDER BY first_name, last_name");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, $row);
}
fclose($out);
$conn->close();
exit; 