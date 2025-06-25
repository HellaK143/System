<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'approved' : 'declined';
    $conn->query("UPDATE bookings SET status='$status' WHERE id=$id");
    // Email notification
    $b = $conn->query("SELECT u.email, r.name FROM bookings b JOIN users u ON b.user_id=u.user_id JOIN resources r ON b.resource_id=r.id WHERE b.id=$id")->fetch_assoc();
    $msg = $status === 'approved' ? 'approved' : 'declined';
    @mail($b['email'], "Booking $msg", "Your booking for resource '{$b['name']}' was $msg.", "From: noreply@umu.ac.ug");
    $_SESSION['msg_success'] = "Booking $msg and user notified.";
    header('Location: admin_bookings.php');
    exit;
}
$res = $conn->query("SELECT b.*, r.name, r.type, u.username FROM bookings b JOIN resources r ON b.resource_id = r.id JOIN users u ON b.user_id = u.user_id WHERE b.status='pending' ORDER BY b.start_datetime");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="../admin_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Admin Dashboard</a>
    <h2>Pending Bookings</h2>
    <?php if (!empty($_SESSION['msg_success'])) { echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['msg_success']).'</div>'; unset($_SESSION['msg_success']); } ?>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Resource</th><th>Type</th><th>User</th><th>Start</th><th>End</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($b = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($b['name']) ?></td>
                <td><?= htmlspecialchars($b['type']) ?></td>
                <td><?= htmlspecialchars($b['username']) ?></td>
                <td><?= htmlspecialchars($b['start_datetime']) ?></td>
                <td><?= htmlspecialchars($b['end_datetime']) ?></td>
                <td><?= htmlspecialchars($b['status']) ?></td>
                <td>
                    <form method="post" style="display:inline">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                        <button name="action" value="decline" class="btn btn-danger btn-sm">Decline</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html> 