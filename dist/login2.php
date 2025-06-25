<?php
session_start();

$con = new mysqli('localhost', 'root', '', 'umic');
if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $con->prepare('SELECT user_id, username, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['password'] === $password) {
            // Set session
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect by role
            switch ($row['role']) {
                case 'admin':
                    header('Location: admin_dashboard.php'); break;
                case 'mentor':
                    header('Location: mentor_dashboard.php'); break;
                case 'evaluator':
                    header('Location: evaluator_dashboard.php'); break;
                case 'entrepreneur':
                default:
                    header('Location: entrepreneur_dashboard.php'); break;
            }
            exit;
        } else {
            $_SESSION['error'] = 'Incorrect email or password.';
        }
    } else {
        $_SESSION['error'] = 'Incorrect email or password.';
    }

    header('Location: login.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}