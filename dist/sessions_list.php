<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) die('Login required.');
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT s.*, a.full_name, u.username AS scheduled_by_name FROM sessions s JOIN applications a ON s.application_id = a.id JOIN users u ON s.scheduled_by = u.user_id WHERE s.scheduled_for = $user_id ORDER BY s.date DESC, s.time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Sessions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="../dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <a href="calendar_view.php" class="btn btn-outline-success mb-3 ms-2">Calendar View</a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="schedule_session.php" class="btn btn-primary mb-3 ms-2">Schedule Session</a>
    <?php endif; ?>
    <h2>My Scheduled Sessions</h2>
    <?php if (!empty($_SESSION['msg_success'])) { echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['msg_success']).'</div>'; unset($_SESSION['msg_success']); } ?>
    <?php if ($res->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Notes</th>
                <th>Status</th>
                <th>Scheduled By</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td><?= htmlspecialchars($s['session_type']) ?></td>
                <td><?= htmlspecialchars($s['date']) ?></td>
                <td><?= htmlspecialchars($s['time']) ?></td>
                <td><?= htmlspecialchars($s['location']) ?></td>
                <td><?= htmlspecialchars($s['notes']) ?></td>
                <td>
                    <?= htmlspecialchars($s['status']) ?>
                    <?php if ($s['status'] === 'scheduled'): ?>
                        <form method="post" action="update_session_status.php" style="display:inline">
                            <input type="hidden" name="session_id" value="<?= $s['id'] ?>">
                            <button name="action" value="confirm" class="btn btn-success btn-sm">Confirm</button>
                            <button name="action" value="decline" class="btn btn-danger btn-sm">Decline</button>
                            <button name="action" value="reschedule" class="btn btn-warning btn-sm">Request Reschedule</button>
                        </form>
                    <?php elseif ($s['status'] === 'declined'): ?>
                        <span class="text-danger">Declined</span>
                    <?php elseif ($s['status'] === 'reschedule_requested'): ?>
                        <span class="text-warning">Reschedule Requested</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($s['scheduled_by_name']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No sessions scheduled for you yet.</div>
    <?php endif; ?>
</div>
</body>
</html> 