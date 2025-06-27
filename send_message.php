<?php
// send_message.php
session_start();
require_once 'db.php'; // expects $host, $user, $password, $dbname

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = intval($_POST['application_id'] ?? 0);
    $recipient_email = trim($_POST['recipient_email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $sender = $_SESSION['email'] ?? 'system';
    $redirect_page = '/system2/application_view.php';
    $is_mentor = ($_SESSION['role'] ?? '') === 'mentor';
    if ($is_mentor) {
        $redirect_page = '/system2/dist/application_view_mentor.php';
    }
    if ($application_id && $recipient_email && $message) {
        $conn = new mysqli($host, $user, $password, $dbname);
        if (!$conn->connect_error) {
            $stmt = $conn->prepare("INSERT INTO messages (application_id, sender, recipient, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
            if (!$stmt) {
                $_SESSION['msg_error'] = "Prepare failed: " . $conn->error;
                header("Location: $redirect_page?id=$application_id");
                exit;
            }
            $stmt->bind_param('isss', $application_id, $sender, $recipient_email, $message);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            // Send email
            $subject = "Message from UMU Innovation Office";
            $headers = "From: noreply@umu.ac.ug\r\nContent-Type: text/plain; charset=UTF-8";
            @mail($recipient_email, $subject, $message, $headers);
            $_SESSION['msg_success'] = "Message sent successfully.";
        } else {
            $_SESSION['msg_error'] = "Database connection failed.";
        }
    } else {
        $_SESSION['msg_error'] = "Missing required fields.";
    }
    if ($application_id > 0) {
        header("Location: $redirect_page?id=$application_id");
    } else {
        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header("Location: $back");
    }
    exit;
}
// fallback
header('Location: index.php');
exit; 