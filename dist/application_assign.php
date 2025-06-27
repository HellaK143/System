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
    
    if ($assigned_mentor) {
        // Get mentor email
        $mentor_stmt = $conn->prepare("SELECT email FROM mentors WHERE mentor_id = ?");
        $mentor_stmt->bind_param("i", $assigned_mentor);
        $mentor_stmt->execute();
        $mentor_result = $mentor_stmt->get_result();
        $mentor_email = null;
        if ($mentor_row = $mentor_result->fetch_assoc()) {
            $mentor_email = $mentor_row['email'];
        }
        $mentor_stmt->close();
        
        // Update both fields
        $stmt = $conn->prepare("UPDATE applications SET assigned_mentor = ?, assigned_mentor_email = ? WHERE id = ?");
        if (!$stmt) {
            die("<div class='alert alert-danger'>SQL Prepare Error: " . htmlspecialchars($conn->error) . "</div>");
        }
        $stmt->bind_param("isi", $assigned_mentor, $mentor_email, $id);
    } else {
        // Clear both fields
        $stmt = $conn->prepare("UPDATE applications SET assigned_mentor = NULL, assigned_mentor_email = NULL WHERE id = ?");
        if (!$stmt) {
            die("<div class='alert alert-danger'>SQL Prepare Error: " . htmlspecialchars($conn->error) . "</div>");
        }
        $stmt->bind_param("i", $id);
    }
    
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