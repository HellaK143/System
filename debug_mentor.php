<?php
session_start();
echo "=== SESSION DEBUG ===\n";
echo "Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Session role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo "Session email: " . ($_SESSION['email'] ?? 'NOT SET') . "\n";

require 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');

$user_id = $_SESSION['user_id'] ?? 0;
echo "\n=== USER DETAILS ===\n";
$user_res = $conn->query("SELECT user_id, username, email, role FROM users WHERE user_id = $user_id");
if ($user_res && $u = $user_res->fetch_assoc()) {
    echo "User ID: " . $u['user_id'] . "\n";
    echo "Username: " . $u['username'] . "\n";
    echo "Email: " . $u['email'] . "\n";
    echo "Role: " . $u['role'] . "\n";
} else {
    echo "User not found!\n";
}

echo "\n=== MENTOR DETAILS ===\n";
$mentor_email = $u['email'] ?? '';
if ($mentor_email) {
    $mentor_res = $conn->query("SELECT mentor_id, full_name, email FROM mentors WHERE email = '" . $conn->real_escape_string($mentor_email) . "'");
    if ($mentor_res && $m = $mentor_res->fetch_assoc()) {
        echo "Mentor ID: " . $m['mentor_id'] . "\n";
        echo "Mentor Name: " . $m['full_name'] . "\n";
        echo "Mentor Email: " . $m['email'] . "\n";
    } else {
        echo "Mentor not found in mentors table!\n";
    }
}

echo "\n=== APPLICATIONS QUERY DEBUG ===\n";
$where = [];
if ($mentor_email) $where[] = "assigned_mentor_email = '" . $conn->real_escape_string($mentor_email) . "'";
if (isset($m['mentor_id'])) $where[] = "assigned_mentor = " . $m['mentor_id'];
$where_sql = $where ? ('WHERE ' . implode(' OR ', $where)) : '';
$sql = "SELECT * FROM applications $where_sql ORDER BY submitted_at DESC";
echo "SQL Query: " . $sql . "\n";

$mentees = [];
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) $mentees[] = $row;
    echo "Found " . count($mentees) . " mentees\n";
} else {
    echo "Query failed: " . $conn->error . "\n";
}

echo "\n=== ALL APPLICATIONS WITH MENTORS ===\n";
$all_res = $conn->query("SELECT id, full_name, email, assigned_mentor, assigned_mentor_email FROM applications WHERE assigned_mentor IS NOT NULL OR assigned_mentor_email IS NOT NULL");
while ($row = $all_res->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Name: " . $row['full_name'] . ", Mentor ID: " . ($row['assigned_mentor'] ?? 'NULL') . ", Mentor Email: " . ($row['assigned_mentor_email'] ?? 'NULL') . "\n";
}

$conn->close();
?> 