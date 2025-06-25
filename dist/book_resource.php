<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) die('Login required.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$id = intval($_GET['id'] ?? 0);
$res = $conn->query("SELECT * FROM resources WHERE id = $id");
if (!$res || $res->num_rows === 0) die('Resource not found.');
$resource = $res->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO bookings (resource_id, user_id, start_datetime, end_datetime) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $id, $user_id, $start, $end);
    $stmt->execute();
    $stmt->close();
    $_SESSION['msg_success'] = 'Booking request submitted.';
    header('Location: bookings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Resource</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="resources.php" class="btn btn-secondary mb-3">&larr; Back to Resources</a>
    <h2>Book Resource: <?= htmlspecialchars($resource['name']) ?></h2>
    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="start_datetime" class="form-label">Start Date/Time</label>
            <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_datetime" class="form-label">End Date/Time</label>
            <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Book</button>
    </form>
</div>
</body>
</html> 