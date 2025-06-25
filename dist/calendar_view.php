<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) die('Login required.');
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT * FROM sessions WHERE scheduled_for = $user_id ORDER BY date, time");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="sessions_list.php" class="btn btn-secondary mb-3">&larr; Back to Sessions List</a>
    <h2>Session Calendar</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($s['date']) ?></td>
                <td><?= htmlspecialchars($s['time']) ?></td>
                <td><?= htmlspecialchars($s['session_type']) ?></td>
                <td><?= htmlspecialchars($s['location']) ?></td>
                <td><?= htmlspecialchars($s['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html> 