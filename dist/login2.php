<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$con = new mysqli('localhost', 'root', '', 'umic');
if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $con->prepare('SELECT user_id, username, password, role, email FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['password'] === $password) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'] . '|' . $_SERVER['REMOTE_ADDR']);

            // Use absolute paths for all redirects
            if ($row['role'] === 'admin') {
                header('Location: /system2/admin_dashboard.php');
            } elseif ($row['role'] === 'mentor') {
                header('Location: /system2/dist/mentor_dashboard.php');
            } elseif ($row['role'] === 'evaluator') {
                header('Location: /system2/dist/evaluator_dashboard.php');
            } elseif ($row['role'] === 'entrepreneur') {
                if (file_exists(__DIR__ . '/entrepreneur_dashboard2.php')) {
                    header('Location: /system2/dist/entrepreneur_dashboard2.php');
                } elseif (file_exists(__DIR__ . '/entrepreneur_dashboard.php')) {
                    header('Location: /system2/dist/entrepreneur_dashboard.php');
                } else {
                    header('Location: /system2/entrepreneur_dashboard.php');
                }
            } else {
                $_SESSION['error'] = 'Unknown user role.';
                header('Location: /system2/login.php');
            }
            exit;
        }
    }
    $_SESSION['error'] = 'Incorrect email or password.';
    header('Location: /system2/login.php');
    exit;
} else {
    header('Location: /system2/login.php');
    exit;
}