<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=mentors_export.csv');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Full Name', 'Email', 'Expertise Area', 'Phone']);
$res = $conn->query("SELECT mentor_id, full_name, email, expertise_area, phone FROM mentors ORDER BY full_name");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, $row);
}
fclose($out);
$conn->close();
exit; 