<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$username || !$email || !$role || !$password) {
        header('Location: users_admin.php?error=Missing+fields'); exit;
    }
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) die('DB error');
    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $role = $conn->real_escape_string($role);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $check = $conn->query("SELECT user_id FROM users WHERE email='$email' OR username='$username'");
    if ($check->num_rows > 0) {
        header('Location: users_admin.php?error=User+already+exists'); exit;
    }
    $sql = "INSERT INTO users (username, email, role, password) VALUES ('$username', '$email', '$role', '$password_hash')";
    if ($conn->query($sql)) {
        header('Location: users_admin.php?success=User+created'); exit;
    } else {
        header('Location: users_admin.php?error=DB+error'); exit;
    }
} else {
    header('Location: users_admin.php'); exit;
} 