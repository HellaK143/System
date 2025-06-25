<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM entrepreneur ORDER BY registration_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Entrepreneurs List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
        }
        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<h2>Registered Entrepreneurs</h2>

<table>
    <tr>
        <th>#</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Student ID</th>
        <th>Department</th>
        <th>Course</th>
        <th>Year</th>
        <th>Gender</th>
        <th>Registered On</th>
    </tr>

    <?php if ($result->num_rows > 0):
        $counter = 1;
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++; ?></td>
                <td>
                    <?php if (!empty($row['profile_picture'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['profile_picture']) ?>" alt="Profile">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['course']) ?></td>
                <td><?= htmlspecialchars($row['year_of_study']) ?></td>
                <td><?= htmlspecialchars($row['gender']) ?></td>
                <td><?= htmlspecialchars($row['registration_date']) ?></td>
            </tr>
        <?php endwhile;
    else: ?>
        <tr>
            <td colspan="11">No entrepreneurs found.</td>
        </tr>
    <?php endif; ?>

</table>

</body>
</html>

<?php
$conn->close();
?>
