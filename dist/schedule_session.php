<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied.');
}
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
// Fetch applications and users for dropdowns
$applications = $conn->query("SELECT id, full_name FROM applications ORDER BY full_name");
$users = $conn->query("SELECT user_id, username, role FROM users WHERE role IN ('mentor','evaluator','entrepreneur','applicant') ORDER BY username");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = intval($_POST['application_id']);
    $session_type = $_POST['session_type'];
    $scheduled_for = intval($_POST['scheduled_for']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = trim($_POST['location']);
    $notes = trim($_POST['notes']);
    $scheduled_by = $_SESSION['user_id'];
    $status = 'scheduled';
    $stmt = $conn->prepare("INSERT INTO sessions (application_id, session_type, scheduled_by, scheduled_for, date, time, location, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isissssss', $application_id, $session_type, $scheduled_by, $scheduled_for, $date, $time, $location, $notes, $status);
    $stmt->execute();
    $stmt->close();
    // Fetch participant email
    $res = $conn->query("SELECT email FROM users WHERE user_id = $scheduled_for");
    $row = $res->fetch_assoc();
    $to = $row['email'];
    $subject = "UMU Session Scheduled";
    $msg = "A new $session_type session has been scheduled for you on $date at $time, location: $location.";
    $headers = "From: noreply@umu.ac.ug\r\nContent-Type: text/plain; charset=UTF-8";
    @mail($to, $subject, $msg, $headers);
    $_SESSION['msg_success'] = "Session scheduled and participant notified.";
    header('Location: schedule_session.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Session</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="../admin_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Admin Dashboard</a>
    <a href="sessions_list.php" class="btn btn-info mb-3 ms-2">View All Sessions</a>
    <a href="calendar_view.php" class="btn btn-outline-success mb-3 ms-2">Calendar View</a>
    <h2>Schedule Interview or Pitch Session</h2>
    <?php if (!empty($_SESSION['msg_success'])) { echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['msg_success']).'</div>'; unset($_SESSION['msg_success']); } ?>
    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="application_id" class="form-label">Applicant</label>
            <select name="application_id" id="application_id" class="form-select" required>
                <option value="">Select Applicant</option>
                <?php while ($a = $applications->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['full_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="session_type" class="form-label">Session Type</label>
            <select name="session_type" id="session_type" class="form-select" required>
                <option value="interview">Interview</option>
                <option value="pitch">Pitch</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="scheduled_for" class="form-label">Participant</label>
            <select name="scheduled_for" id="scheduled_for" class="form-select" required>
                <option value="">Select Participant</option>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <option value="<?= $u['user_id'] ?>"><?= htmlspecialchars($u['username']) ?> (<?= $u['role'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Time</label>
            <input type="time" name="time" id="time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Schedule Session</button>
    </form>
</div>
</body>
</html> 