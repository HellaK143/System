<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name && $type) {
        $stmt = $conn->prepare('INSERT INTO resources (name, type, description) VALUES (?, ?, ?)');
        if (!$stmt) {
            $msg = '<div class="alert alert-danger">Prepare failed: ' . htmlspecialchars($conn->error) . '</div>';
        } else {
            $stmt->bind_param('sss', $name, $type, $desc);
            if ($stmt->execute()) {
                $msg = '<div class="alert alert-success">Resource added!</div>';
            } else {
                $msg = '<div class="alert alert-danger">Error: ' . htmlspecialchars($stmt->error) . '</div>';
            }
            $stmt->close();
        }
    } else {
        $msg = '<div class="alert alert-danger">Name and type are required.</div>';
    }
}
$res = $conn->query('SELECT * FROM resources ORDER BY id DESC');
ob_start();
?>
<div class="container-fluid px-4">
    <h2 class="mb-4">Manage Resources</h2>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <?= $msg ?>
    <form method="post" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="">Select type</option>
                    <option value="lab">Lab</option>
                    <option value="co-working">Co-Working</option>
                    <option value="equipment">Equipment</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </div>
    </form>
    <h4>Existing Resources</h4>
    <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Description</th></tr></thead>
        <tbody>
        <?php if ($res && $res->num_rows > 0): while ($r = $res->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($r['id']) ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['type']) ?></td>
                <td><?= htmlspecialchars($r['description']) ?></td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="4" class="text-center text-muted">No resources found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$page_content = ob_get_clean();
$page_title = 'Manage Resources';
$breadcrumb_items = ['Resources'];
include __DIR__ . '/dist/template_admin.php'; 