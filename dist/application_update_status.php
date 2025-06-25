<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $feedback = isset($_POST['feedback']) ? $_POST['feedback'] : '';
    $allowed_statuses = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
    if (!in_array($status, $allowed_statuses)) {
        die('Invalid status value.');
    }
    $stmt = $conn->prepare("UPDATE applications SET status = ?, feedback = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $feedback, $id);
    if ($stmt->execute()) {
        header("Location: application_view.php?id=$id&updated=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Update failed: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
}
$conn->close(); 