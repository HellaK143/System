<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";

// Connect to database
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set headers to force download as Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=entrepreneurs_export.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Output column headers
echo "Full Name\tEmail\tPhone\tStudent ID\tDepartment\tCourse\tYear\tGender\tRole\tSector\tInterests\tRegistration Date\n";

// Query data
$sql = "SELECT * FROM entrepreneur ORDER BY registration_date DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $fullName = $row['first_name'] . ' ' . $row['last_name'];
    echo $fullName . "\t" .
         $row['email'] . "\t" .
         $row['phone'] . "\t" .
         $row['student_id'] . "\t" .
         $row['department'] . "\t" .
         $row['course'] . "\t" .
         $row['year_of_study'] . "\t" .
         $row['gender'] . "\t" .
         $row['role'] . "\t" .
         $row['sector'] . "\t" .
         $row['interests'] . "\t" .
         $row['registration_date'] . "\n";
}

$conn->close();
exit;
?>
