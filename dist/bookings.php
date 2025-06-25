<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) die('Login required.');
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT b.*, r.name, r.type FROM bookings b JOIN resources r ON b.resource_id = r.id WHERE b.user_id = $user_id ORDER BY b.start_datetime DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="resources.php" class="btn btn-secondary mb-3">&larr; Back to Resources</a>
    <h2>My Bookings</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Resource</th><th>Type</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
        <tbody>
        <?php while ($b = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($b['name']) ?></td>
                <td><?= htmlspecialchars($b['type']) ?></td>
                <td><?= htmlspecialchars($b['start_datetime']) ?></td>
                <td><?= htmlspecialchars($b['end_datetime']) ?></td>
                <td><?= htmlspecialchars($b['status']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html> 