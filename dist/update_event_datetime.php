<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Not allowed');
$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);
$start = $data['start'];
$end = $data['end'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$stmt = $conn->prepare("UPDATE events SET start_datetime=?, end_datetime=? WHERE id=?");
$stmt->bind_param('ssi', $start, $end, $id);
$stmt->execute();
$stmt->close();
$conn->close();
echo 'ok'; 