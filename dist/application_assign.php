<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $assigned_mentor = isset($_POST['assigned_mentor']) ? intval($_POST['assigned_mentor']) : null;
    $stmt = $conn->prepare("UPDATE applications SET assigned_mentor = ? WHERE id = ?");
    if (!$stmt) {
        die("<div class='alert alert-danger'>SQL Prepare Error: " . htmlspecialchars($conn->error) . "</div>");
    }
    $stmt->bind_param("ii", $assigned_mentor, $id);
    if ($stmt->execute()) {
        header("Location: applications.php?assigned=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Assignment failed: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
}
$conn->close(); 