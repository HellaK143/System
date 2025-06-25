<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    $_SESSION['error'] = "Database connection failed.";
    header('Location: ../register.html');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    if (!$first_name || !$last_name || !$email || !$password || !$confirm_password || !$role) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../register.html');
        exit();
    }
    if ($role === 'admin') {
        $_SESSION['error'] = "Registration as admin is not allowed.";
        header('Location: ../register.html');
        exit();
    }
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: ../register.html');
        exit();
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
        header('Location: ../register.html');
        exit();
    }
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered.";
        $stmt->close();
        header('Location: ../register.html');
        exit();
    }
    $stmt->close();
    // Insert user (plain text password)
    $full_name = $first_name . ' ' . $last_name;
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $password, $role);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        $stmt->close();
        $conn->close();
        header('Location: ../login.php');
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        $stmt->close();
        $conn->close();
        header('Location: ../register.html');
        exit();
    }
} else {
    header('Location: ../register.html');
    exit();
} 