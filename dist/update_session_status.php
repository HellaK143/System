<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) die('Login required.');
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = intval($_POST['session_id']);
    $action = $_POST['action'];
    $allowed = ['confirm' => 'confirmed', 'decline' => 'declined', 'reschedule' => 'reschedule_requested'];
    if (!isset($allowed[$action])) die('Invalid action.');
    $new_status = $allowed[$action];
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) die('DB error');
    // Only allow if user is scheduled_for
    $res = $conn->query("SELECT scheduled_for FROM sessions WHERE id = $session_id");
    $row = $res->fetch_assoc();
    if (!$row || $row['scheduled_for'] != $user_id) die('Not allowed.');
    $stmt = $conn->prepare("UPDATE sessions SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $new_status, $session_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    $_SESSION['msg_success'] = "Session status updated to $new_status.";
    header('Location: sessions_list.php');
    exit;
}
header('Location: sessions_list.php');
exit; 