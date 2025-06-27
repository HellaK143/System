<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: users_admin.php?error=Invalid+user+ID'); exit; }
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
if ($conn->query("DELETE FROM users WHERE user_id=$id")) {
    header('Location: users_admin.php?success=User+deleted'); exit;
} else {
    header('Location: users_admin.php?error=DB+error'); exit;
} 