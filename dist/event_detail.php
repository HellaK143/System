<?php
session_start();
require_once '../db.php';
$id = intval($_GET['id'] ?? 0);
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT * FROM events WHERE id = $id");
if (!$res || $res->num_rows === 0) die('Event not found.');
$e = $res->fetch_assoc();
$user_id = $_SESSION['user_id'] ?? 0;
// Check invitation/registration status
$invited = $conn->query("SELECT status FROM event_invitations WHERE event_id = $id AND user_id = $user_id")->fetch_assoc()['status'] ?? null;
$registered = $conn->query("SELECT id FROM event_registrations WHERE event_id = $id AND user_id = $user_id")->num_rows > 0;
?>
<div class="mb-2"><span class="badge bg-primary">Type: <?= htmlspecialchars($e['event_type']) ?></span></div>
<h4><?= htmlspecialchars($e['title']) ?></h4>
<div class="mb-2"><b>Date/Time:</b> <?= date('D, d M Y H:i', strtotime($e['start_datetime'])) ?> - <?= date('H:i', strtotime($e['end_datetime'])) ?></div>
<div class="mb-2"><b>Location:</b> <?= htmlspecialchars($e['location']) ?></div>
<div class="mb-2"><b>Description:</b> <?= nl2br(htmlspecialchars($e['description'])) ?></div>
<?php if ($invited): ?>
  <div class="alert alert-info">You are invited to this event (Status: <?= htmlspecialchars($invited) ?>).</div>
<?php endif; ?>
<?php if ($registered): ?>
  <div class="alert alert-success">You are registered for this event.</div>
<?php endif; ?> 