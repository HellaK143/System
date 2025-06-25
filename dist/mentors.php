<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "umic");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mentor_id'])) {
    $mentor_id = intval($_POST['mentor_id']);
    $stmt = $conn->prepare("DELETE FROM mentors WHERE mentor_id = ?");
    $stmt->bind_param("i", $mentor_id);
    $stmt->execute();
    $stmt->close();
    // Redirect to avoid resubmission
    header("Location: mentors.php");
    exit();
}

// Fetch mentors from DB
$sql = "SELECT * FROM mentors ORDER BY full_name ASC";
$result = $conn->query($sql);
if (!$result) {
    die("Error fetching mentors: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">Mentors</h2>
        <div class="mb-3">
            <a href="add_mentor.php" class="btn btn-success me-2 mb-2"><i class="fas fa-plus"></i> Register New Mentor</a>
            <a href="export_mentors.php" class="btn btn-primary me-2 mb-2"><i class="fas fa-file-export"></i> Export to Excel</a>
            <a href="match_mentor.php" class="btn btn-info mb-2"><i class="fas fa-random"></i> Match Mentors</a>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-chalkboard-teacher"></i> Mentor List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Expertise Area</th>
                                <th>Phone</th>
                                <th>Assigned Department</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0):
                                $i = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                                        <td><?= htmlspecialchars($row['email']); ?></td>
                                        <td><?= htmlspecialchars($row['expertise_area']); ?></td>
                                        <td><?= htmlspecialchars($row['phone']); ?></td>
                                        <td><?= htmlspecialchars($row['assigned_department']); ?></td>
                                        <td class="actions">
                                            <a href="edit_mentor.php?mentor_id=<?= $row['mentor_id']; ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="match_mentor.php?id=<?= $row['mentor_id']; ?>" class="btn btn-sm btn-info me-1"><i class="fas fa-random"></i> Match</a>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this mentor?');">
                                                <input type="hidden" name="mentor_id" value="<?= $row['mentor_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr><td colspan="7" class="text-center">No mentors found.</td></tr>
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
