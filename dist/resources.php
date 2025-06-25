<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) die('Login required.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT * FROM resources ORDER BY type, name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="../dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <a href="bookings.php" class="btn btn-info mb-3 ms-2">View My Bookings</a>
    <h2>Resources</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Name</th><th>Type</th><th>Description</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($r = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['type']) ?></td>
                <td><?= htmlspecialchars($r['description']) ?></td>
                <td><a href="book_resource.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">Book</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html> 