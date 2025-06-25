<?php
session_start();

$con = new mysqli('localhost', 'root', '', 'umic');
if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Allow only these roles
    $allowed_roles = ['mentor', 'entrepreneur', 'evaluator'];
    if (!in_array($role, $allowed_roles)) {
        $_SESSION['error'] = 'Invalid role selected.';
        header('Location: register.php');
        exit;
    }

    // Check if username or email already exist
    $checkStmt = $con->prepare('SELECT user_id FROM users WHERE username = ? OR email = ?');
    $checkStmt->bind_param('ss', $username, $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['error'] = 'Username or email already in use.';
        header('Location: register.php');
        exit;
    }

    // Insert new user
    $stmt = $con->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $username, $email, $password, $role);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Account created successfully. Please log in.';
        header('Location: login.php');
    } else {
        $_SESSION['error'] = 'Error creating account. Please try again.';
        header('Location: register.php');
    }
    exit;
} else {
    header('Location: register.php');
    exit;
}