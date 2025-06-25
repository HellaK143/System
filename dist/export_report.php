<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$format = $_POST['format'] ?? 'csv';
$category = $_POST['category'] ?? '';
$status = $_POST['status'] ?? '';
$date_from = $_POST['date_from'] ?? '';
$date_to = $_POST['date_to'] ?? '';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$where = [];
if ($category) $where[] = "category = '".$conn->real_escape_string($category)."'";
if ($status) $where[] = "status = '".$conn->real_escape_string($status)."'";
if ($date_from) $where[] = "submitted_at >= '".$conn->real_escape_string($date_from)."'";
if ($date_to) $where[] = "submitted_at <= '".$conn->real_escape_string($date_to)." 23:59:59'";
$where_sql = $where ? ('WHERE '.implode(' AND ', $where)) : '';
$res = $conn->query("SELECT id, full_name, category, status, submitted_at FROM applications $where_sql ORDER BY submitted_at DESC");
$filename = 'applicants_report_' . date('Ymd_His');
if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$filename.'.xls"');
    echo "ID\tName\tCategory\tStatus\tSubmitted\n";
    while ($row = $res->fetch_assoc()) {
        echo $row['id']."\t".str_replace(["\t","\n"],' ',$row['full_name'])."\t".str_replace(["\t","\n"],' ',$row['category'])."\t".$row['status']."\t".$row['submitted_at']."\n";
    }
    exit;
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Name','Category','Status','Submitted']);
    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [$row['id'], $row['full_name'], $row['category'], $row['status'], $row['submitted_at']]);
    }
    fclose($out);
    exit;
} 