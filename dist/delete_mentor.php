<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn = new mysqli("localhost", "root", "", "umic");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM Mentor WHERE mentor_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: mentors.php?deleted=1");
    } else {
        echo "Error deleting mentor: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: mentors.php");
    exit;
}
