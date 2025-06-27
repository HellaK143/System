<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mentor'])) {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $expertise = trim($_POST['expertise_area']);
    $phone = trim($_POST['phone']);
    $assigned_department = trim($_POST['assigned_department']);
    $stmt = $conn->prepare("INSERT INTO mentors (full_name, email, expertise_area, phone, assigned_department) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $name, $email, $expertise, $phone, $assigned_department);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_mentors_admin.php'); exit;
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_mentor'])) {
    $id = intval($_POST['mentor_id']);
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $expertise = trim($_POST['expertise_area']);
    $phone = trim($_POST['phone']);
    $assigned_department = trim($_POST['assigned_department']);
    $stmt = $conn->prepare("UPDATE mentors SET full_name=?, email=?, expertise_area=?, phone=?, assigned_department=? WHERE mentor_id=?");
    $stmt->bind_param('sssssi', $name, $email, $expertise, $phone, $assigned_department, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_mentors_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mentor'])) {
    $id = intval($_POST['mentor_id']);
    $conn->query("DELETE FROM mentors WHERE mentor_id=$id");
    header('Location: manage_mentors_admin.php'); exit;
}
$mentors = $conn->query("SELECT mentor_id, full_name, email, expertise_area, phone, assigned_department FROM mentors ORDER BY full_name");
$conn->close();
$page_title = 'Manage Mentors';
$breadcrumb_items = ['Mentors', 'Manage Mentors'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Mentors</h2>
    <a href="export_mentors.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addMentorModal">Add New Mentor</button>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search mentors...">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Expertise Area</th>
                    <th>Phone</th>
                    <th>Assigned Department</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Full Name"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Email"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Expertise"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Phone"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Department"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($mentors && $mentors->num_rows > 0): while($m = $mentors->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($m['mentor_id']) ?></td>
                    <td><?= htmlspecialchars($m['full_name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['expertise_area']) ?></td>
                    <td><?= htmlspecialchars($m['phone']) ?></td>
                    <td><?= htmlspecialchars($m['assigned_department']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editMentorModal<?= $m['mentor_id'] ?>">Edit</button>
                        <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this mentor?');">
                            <input type="hidden" name="mentor_id" value="<?= $m['mentor_id'] ?>">
                            <button type="submit" name="delete_mentor" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editMentorModal<?= $m['mentor_id'] ?>" tabindex="-1" aria-labelledby="editMentorModalLabel<?= $m['mentor_id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editMentorModalLabel<?= $m['mentor_id'] ?>">Edit Mentor</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="mentor_id" value="<?= $m['mentor_id'] ?>">
                          <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($m['full_name']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($m['email']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Expertise Area</label><input type="text" name="expertise_area" class="form-control" value="<?= htmlspecialchars($m['expertise_area']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($m['phone']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Assigned Department</label><input type="text" name="assigned_department" class="form-control" value="<?= htmlspecialchars($m['assigned_department']) ?>"></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" name="edit_mentor" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center text-muted">No mentors found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addMentorModal" tabindex="-1" aria-labelledby="addMentorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addMentorModalLabel">Add New Mentor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Expertise Area</label><input type="text" name="expertise_area" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Assigned Department</label><input type="text" name="assigned_department" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_mentor" class="btn btn-primary">Add Mentor</button>
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