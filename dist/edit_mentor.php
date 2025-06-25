<?php
$conn = new mysqli("localhost", "root", "", "umic");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get mentor_id from URL
if (!isset($_GET['mentor_id'])) {
    header("Location: mentors.php");
    exit();
}

$mentor_id = intval($_GET['mentor_id']);

// Fetch mentor data
$stmt = $conn->prepare("SELECT * FROM mentors WHERE mentor_id = ?");
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Mentor not found";
    exit();
}

$mentor = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $expertise_area = $_POST['expertise_area'];
    $phone = $_POST['phone'];
    $assigned_department = $_POST['assigned_department'];

    $update_stmt = $conn->prepare("UPDATE mentors SET full_name = ?, email = ?, expertise_area = ?, phone = ?, assigned_department = ? WHERE mentor_id = ?");
    $update_stmt->bind_param("sssssi", $full_name, $email, $expertise_area, $phone, $assigned_department, $mentor_id);
    if ($update_stmt->execute()) {
        header("Location: mentors.php");
        exit();
    } else {
        echo "Error updating mentor: " . $conn->error;
    }
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Mentor</title>
</head>
<body>

<h2>Edit Mentor</h2>

<form method="post">
    <label>Full Name:<br>
        <input type="text" name="full_name" required value="<?= htmlspecialchars($mentor['full_name']); ?>">
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email" required value="<?= htmlspecialchars($mentor['email']); ?>">
    </label><br><br>

    <label>Expertise Area:<br>
        <input type="text" name="expertise_area" required value="<?= htmlspecialchars($mentor['expertise_area']); ?>">
    </label><br><br>

    <label>Phone:<br>
        <input type="text" name="phone" value="<?= htmlspecialchars($mentor['phone']); ?>">
    </label><br><br>

    <label>Assigned Department:<br>
        <input type="text" name="assigned_department" value="<?= htmlspecialchars($mentor['assigned_department']); ?>">
    </label><br><br>

<a href="mentors.php" style="
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 14px;
    transition: background-color 0.3s ease;
">
    ← Back to Mentors
</a></form>

<br>
<a href="mentors.php" style="
    display: inline-block;
    padding: 8px 16px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease;
">
    ← Back to Mentors
</a>
</body>
</html>
