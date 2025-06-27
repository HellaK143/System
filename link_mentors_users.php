<?php
require 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');

$count = 0;
$res = $conn->query("SELECT mentor_id, email FROM mentors");
while ($mentor = $res->fetch_assoc()) {
    $mentor_id = $mentor['mentor_id'];
    $mentor_email = strtolower(trim($mentor['email']));
    $user_res = $conn->query("SELECT user_id FROM users WHERE LOWER(TRIM(email)) = '" . $conn->real_escape_string($mentor_email) . "'");
    if ($user_res && $user = $user_res->fetch_assoc()) {
        $user_id = $user['user_id'];
        $conn->query("UPDATE mentors SET user_id = $user_id WHERE mentor_id = $mentor_id");
        echo "Linked mentor_id $mentor_id to user_id $user_id ($mentor_email)<br>\n";
        $count++;
    }
}
$conn->close();
echo "<br>Done. Linked $count mentors to users.";
?> 