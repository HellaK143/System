<?php
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('[]');
$res = $conn->query("SELECT id, title, start_datetime AS start, end_datetime AS end, event_type, location, description FROM events");
$events = [];
while ($e = $res->fetch_assoc()) {
    $e['allDay'] = false;
    $events[] = $e;
}
header('Content-Type: application/json');
echo json_encode($events); 