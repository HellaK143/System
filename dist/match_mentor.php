<?php
$conn = new mysqli("localhost", "root", "", "umic");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Check if 'id' is present and is a positive integer
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || intval($_GET['id']) <= 0) {
    die("Invalid mentor ID.");
}

$mentor_id = intval($_GET['id']);

// Use correct table name 'mentors' here (plural)
$stmt = $conn->prepare("SELECT * FROM mentors WHERE mentor_id = ?");
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Mentor not found.");
}

$mentor = $result->fetch_assoc();

// Sanitize assigned_department before query
$dept = $conn->real_escape_string($mentor['assigned_department']);

$matches = $conn->query("SELECT * FROM entrepreneur WHERE department = '$dept'");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mentor Match</title>
</head>
<body>
    <h2>Mentor: <?= htmlspecialchars($mentor['full_name']) ?> - <?= htmlspecialchars($mentor['expertise_area']) ?></h2>
    <h3>Matched Entrepreneurs (Department: <?= htmlspecialchars($dept) ?>)</h3>
    <ul>
        <?php if ($matches && $matches->num_rows > 0): ?>
            <?php while ($e = $matches->fetch_assoc()): ?>
                <li><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?> - <?= htmlspecialchars($e['course']) ?></li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No matches found.</li>
        <?php endif; ?>
    </ul>

    <a href="mentors.php">← Back to Mentor List</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
