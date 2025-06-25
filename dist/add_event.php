<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $type = $_POST['event_type'];
    $start = $_POST['start_datetime'];
    $end = $_POST['end_datetime'];
    $loc = trim($_POST['location']);
    $created_by = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO events (title, description, event_type, start_datetime, end_datetime, location, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssi', $title, $desc, $type, $start, $end, $loc, $created_by);
    $stmt->execute();
    $stmt->close();
    $_SESSION['msg_success'] = 'Event added.';
    header('Location: events_calendar.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="events_calendar.php" class="btn btn-secondary mb-3">&larr; Back to Calendar</a>
    <h2>Add Event</h2>
    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="event_type" class="form-label">Type</label>
            <select name="event_type" id="event_type" class="form-select" required>
                <option value="workshop">Workshop</option>
                <option value="training">Training</option>
                <option value="mentoring">Mentoring</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="start_datetime" class="form-label">Start Date/Time</label>
            <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_datetime" class="form-label">End Date/Time</label>
            <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Event</button>
    </form>
</div>
</body>
</html> 