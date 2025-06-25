<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');
$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) die(json_encode(['success'=>false,'message'=>'Login required.']));
$data = json_decode(file_get_contents('php://input'), true);
$event_id = intval($data['event_id']);
$action = $data['action'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die(json_encode(['success'=>false,'message'=>'DB error']));
if ($action === 'accept' || $action === 'decline') {
    $status = $action === 'accept' ? 'accepted' : 'declined';
    $conn->query("UPDATE event_invitations SET status='$status' WHERE event_id=$event_id AND user_id=$user_id");
    // Email notification
    $user = $conn->query("SELECT email FROM users WHERE user_id=$user_id")->fetch_assoc();
    $event = $conn->query("SELECT title FROM events WHERE id=$event_id")->fetch_assoc();
    @mail($user['email'], "Event Invitation $status", "Your invitation for event '{$event['title']}' was $status.", "From: noreply@umu.ac.ug");
    echo json_encode(['success'=>true,'message'=>'Invitation '.$status]);
    exit;
}
if ($action === 'register') {
    $conn->query("INSERT IGNORE INTO event_registrations (event_id, user_id) VALUES ($event_id, $user_id)");
    // Email notification
    $user = $conn->query("SELECT email FROM users WHERE user_id=$user_id")->fetch_assoc();
    $event = $conn->query("SELECT title FROM events WHERE id=$event_id")->fetch_assoc();
    @mail($user['email'], "Event Registration", "You are registered for event '{$event['title']}'.", "From: noreply@umu.ac.ug");
    echo json_encode(['success'=>true,'message'=>'Registered for event']);
    exit;
}
echo json_encode(['success'=>false,'message'=>'Invalid action']); 