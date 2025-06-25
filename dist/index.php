<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch programs for filter
$programs = [];
$progResult = $conn->query("SELECT id, name FROM programs ORDER BY name");
while ($row = $progResult->fetch_assoc()) {
    $programs[] = $row;
}

// Handle program filter
$selected_program = isset($_GET['program_id']) ? intval($_GET['program_id']) : 0;
$where = $selected_program ? "WHERE a.program_id = $selected_program" : "";

// Fetch applications
$sql = "SELECT a.id, a.full_name, a.status, p.name AS program_name FROM applications a LEFT JOIN programs p ON a.program_id = p.id $where ORDER BY a.id DESC";
$applications = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Application Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Application Management</h2>
    <form method="get" class="mb-4 row g-3 align-items-center">
        <div class="col-auto">
            <label for="program_id" class="col-form-label">Filter by Program:</label>
        </div>
        <div class="col-auto">
            <select name="program_id" id="program_id" class="form-select" onchange="this.form.submit()">
                <option value="0">All Programs</option>
                <?php foreach ($programs as $prog): ?>
                    <option value="<?= $prog['id'] ?>" <?= $selected_program == $prog['id'] ? 'selected' : '' ?>><?= htmlspecialchars($prog['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Applicant Name</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($applications && $applications->num_rows > 0): ?>
                <?php while ($app = $applications->fetch_assoc()): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['full_name']) ?></td>
                    <td><?= htmlspecialchars($app['program_name']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($app['status']) ?></span></td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary">View</a>
                        <a href="#" class="btn btn-sm btn-secondary">Assign</a>
                        <a href="#" class="btn btn-sm btn-success">Update</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No applications found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?> 