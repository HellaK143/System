<?php
session_start();
require '../db.php';
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT user_id, username, email, role FROM users WHERE user_id = $user_id");
echo "<pre>";
if ($res && $u = $res->fetch_assoc()) {
    print_r($u);
} else {
    echo "User not found.";
}
$conn->close();
echo "</pre>";
?> 