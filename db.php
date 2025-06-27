<?php
// db.php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'umic';

function log_activity($user_id, $username, $role, $activity, $details = null) {
    global $host, $user, $password, $dbname;
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) return;
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, role, activity, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $user_id, $username, $role, $activity, $details);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function add_notification($user_id, $type, $category, $title, $message) {
    global $host, $user, $password, $dbname;
    $conn = new mysqli($host, $user, $password, $dbname);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, category, title, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $user_id, $type, $category, $title, $message);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

function get_user_id_by_email($email) {
    global $host, $user, $password, $dbname;
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) return null;
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $user_id ?? null;
}

function get_user_ids_by_role($role) {
    global $host, $user, $password, $dbname;
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) return [];
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE role=?");
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];
    while ($row = $result->fetch_assoc()) $ids[] = $row['user_id'];
    $stmt->close();
    $conn->close();
    return $ids;
}

function secure_session_check() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $fingerprint = md5($ua . '|' . $ip);
    if (!isset($_SESSION['fingerprint'])) {
        // Not set, force login
        session_unset(); session_destroy();
        header('Location: login.php'); exit;
    }
    if ($_SESSION['fingerprint'] !== $fingerprint) {
        session_unset(); session_destroy();
        header('Location: login.php'); exit;
    }
} 