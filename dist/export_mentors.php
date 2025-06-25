<?php
$conn = new mysqli("localhost", "root", "", "umic");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=mentors_" . date("Y-m-d") . ".xls");

echo "Full Name\tEmail\tExpertise Area\tPhone\tAssigned Department\n";

$result = $conn->query("SELECT * FROM Mentor");
while ($row = $result->fetch_assoc()) {
    echo $row['full_name'] . "\t" . $row['email'] . "\t" . $row['expertise_area'] . "\t" . $row['phone'] . "\t" . $row['assigned_department'] . "\n";
}

$conn->close();
