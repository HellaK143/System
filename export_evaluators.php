<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=evaluators_export.csv');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Name', 'Email']);
$res = $conn->query("SELECT user_id, username, email FROM users WHERE role='evaluator' ORDER BY username");
while ($row = $res->fetch_assoc()) {
    fputcsv($out, $row);
}
fclose($out);
$conn->close();
exit; 