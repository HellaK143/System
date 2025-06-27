<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_evaluator'])) {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = 'evaluator';
    $status = trim($_POST['status']);
    $stmt = $conn->prepare("INSERT INTO users (username, email, role, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $role, $status);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_evaluators_admin.php'); exit;
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_evaluator'])) {
    $id = intval($_POST['user_id']);
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $status = trim($_POST['status']);
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, status=? WHERE user_id=? AND role='evaluator'");
    $stmt->bind_param('sssi', $name, $email, $status, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_evaluators_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_evaluator'])) {
    $id = intval($_POST['user_id']);
    $conn->query("DELETE FROM users WHERE user_id=$id AND role='evaluator'");
    header('Location: manage_evaluators_admin.php'); exit;
}
$evaluators = $conn->query("SELECT user_id, username, email, status, created_at FROM users WHERE role='evaluator' ORDER BY username");
$conn->close();
$page_title = 'Manage Evaluators';
$breadcrumb_items = ['Evaluators', 'Manage Evaluators'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Evaluators</h2>
    <a href="export_evaluators.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addEvaluatorModal">Add New Evaluator</button>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search evaluators...">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Name"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Email"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Status"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Created At"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($evaluators && $evaluators->num_rows > 0): while($e = $evaluators->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($e['user_id']) ?></td>
                    <td><?= htmlspecialchars($e['username']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= htmlspecialchars($e['status']) ?></td>
                    <td><?= htmlspecialchars($e['created_at']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editEvaluatorModal<?= $e['user_id'] ?>">Edit</button>
                        <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this evaluator?');">
                            <input type="hidden" name="user_id" value="<?= $e['user_id'] ?>">
                            <button type="submit" name="delete_evaluator" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editEvaluatorModal<?= $e['user_id'] ?>" tabindex="-1" aria-labelledby="editEvaluatorModalLabel<?= $e['user_id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editEvaluatorModalLabel<?= $e['user_id'] ?>">Edit Evaluator</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="user_id" value="<?= $e['user_id'] ?>">
                          <div class="mb-3"><label class="form-label">Name</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($e['username']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($e['email']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control" value="<?= htmlspecialchars($e['status']) ?>"></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" name="edit_evaluator" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; else: ?>
                <tr><td colspan="6" class="text-center text-muted">No evaluators found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addEvaluatorModal" tabindex="-1" aria-labelledby="addEvaluatorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addEvaluatorModalLabel">Add New Evaluator</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Name</label><input type="text" name="username" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_evaluator" class="btn btn-primary">Add Evaluator</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
// Global search
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(value) > -1 ? '' : 'none';
    });
});
// Column-specific search
const columnInputs = document.querySelectorAll('.column-search');
columnInputs.forEach(function(input, colIdx) {
    input.addEventListener('keyup', function() {
        var value = this.value.toLowerCase();
        var rows = document.querySelectorAll('#dataTable tbody tr');
        rows.forEach(function(row) {
            var cell = row.querySelectorAll('td')[colIdx+1]; // +1 to skip ID
            if (!cell) return;
            var text = cell.textContent.toLowerCase();
            row.style.display = text.indexOf(value) > -1 ? '' : 'none';
        });
    });
});
</script>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 