<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $conn = new mysqli("localhost", "root", "", "umic");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

$stmt = $conn->prepare("DELETE FROM entrepreneur WHERE entrepreneur_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: entrepreneurs.php?deleted=1");
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: entrepreneurs.php");
    exit;
}
