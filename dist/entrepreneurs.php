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
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrepreneurs List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">Registered Entrepreneurs</h2>
        <div class="mb-3">
            <a href="add_entrepreneur.php" class="btn btn-success me-2 mb-2"><i class="fas fa-plus"></i> Register New Entrepreneur</a>
            <a href="export_entrepreneurs.php" class="btn btn-primary mb-2"><i class="fas fa-file-export"></i> Export to Excel</a>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-users"></i> Entrepreneurs List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Student ID</th>
                                <th>Role</th>
                                <th>Sector</th>
                                <th>Interests</th>
                                <th>Department</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Gender</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0):
                                $counter = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $counter++; ?></td>
                                        <td>
                                            <?php if (!empty($row['profile_picture'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($row['profile_picture']) ?>" alt="Profile" style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                                        <td><?= htmlspecialchars($row['role']) ?></td>
                                        <td><?= htmlspecialchars($row['sector']) ?></td>
                                        <td><?= htmlspecialchars($row['interests']) ?></td>
                                        <td><?= htmlspecialchars($row['department']) ?></td>
                                        <td><?= htmlspecialchars($row['course']) ?></td>
                                        <td><?= htmlspecialchars($row['year_of_study']) ?></td>
                                        <td><?= htmlspecialchars($row['gender']) ?></td>
                                        <td><?= htmlspecialchars($row['registration_date']) ?></td>
                                        <td>
                                            <a href="edit_entrepreneur.php?id=<?= $row['entrepreneur_id'] ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                            <form method="post" action="delete_entrepreneur.php" class="d-inline" onsubmit="return confirm('Delete this entrepreneur?');">
                                                <input type="hidden" name="entrepreneur_id" value="<?= $row['entrepreneur_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr><td colspan="15" class="text-center">No entrepreneurs found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html>

<?php
$conn->close();
?>
