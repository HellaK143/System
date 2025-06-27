<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_session'])) {
    $type = trim($_POST['session_type']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);
    $stmt = $conn->prepare("INSERT INTO sessions (session_type, date, time, location, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $type, $date, $time, $location, $status);
    $stmt->execute();
    $stmt->close();
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'session', 'Session Added', 'A new session ($type) was scheduled for $date at $time in $location.');
    }
    header('Location: manage_sessions_admin.php'); exit;
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_session'])) {
    $id = intval($_POST['session_id']);
    $type = trim($_POST['session_type']);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);
    $stmt = $conn->prepare("UPDATE sessions SET session_type=?, date=?, time=?, location=?, status=? WHERE id=?");
    $stmt->bind_param('sssssi', $type, $date, $time, $location, $status, $id);
    $stmt->execute();
    $stmt->close();
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'session', 'Session Edited', 'Session #'.$id.' ($type) was updated.');
    }
    header('Location: manage_sessions_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_session'])) {
    $id = intval($_POST['session_id']);
    $conn->query("DELETE FROM sessions WHERE id=$id");
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'error', 'session', 'Session Deleted', 'Session #'.$id.' was deleted.');
    }
    header('Location: manage_sessions_admin.php'); exit;
}
$sessions = $conn->query("SELECT id, session_type, date, time, location, status FROM sessions ORDER BY date DESC, time DESC");
$conn->close();
$page_title = 'Manage Sessions';
$breadcrumb_items = ['Sessions', 'Manage Sessions'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Sessions</h2>
    <a href="export_sessions.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addSessionModal">Add New Session</button>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search sessions...">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Type"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Date"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Time"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Location"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Status"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sessions && $sessions->num_rows > 0): while($s = $sessions->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($s['id']) ?></td>
                    <td><?= htmlspecialchars($s['session_type']) ?></td>
                    <td><?= htmlspecialchars($s['date']) ?></td>
                    <td><?= htmlspecialchars($s['time']) ?></td>
                    <td><?= htmlspecialchars($s['location']) ?></td>
                    <td><?= htmlspecialchars($s['status']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editSessionModal<?= $s['id'] ?>">Edit</button>
                        <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this session?');">
                            <input type="hidden" name="session_id" value="<?= $s['id'] ?>">
                            <button type="submit" name="delete_session" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editSessionModal<?= $s['id'] ?>" tabindex="-1" aria-labelledby="editSessionModalLabel<?= $s['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editSessionModalLabel<?= $s['id'] ?>">Edit Session</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="session_id" value="<?= $s['id'] ?>">
                          <div class="mb-3"><label class="form-label">Type</label><input type="text" name="session_type" class="form-control" value="<?= htmlspecialchars($s['session_type']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= htmlspecialchars($s['date']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Time</label><input type="time" name="time" class="form-control" value="<?= htmlspecialchars($s['time']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Location</label><input type="text" name="location" class="form-control" value="<?= htmlspecialchars($s['location']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control" value="<?= htmlspecialchars($s['status']) ?>" required></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" name="edit_session" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center text-muted">No sessions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addSessionModalLabel">Add New Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Type</label><input type="text" name="session_type" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Time</label><input type="time" name="time" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Location</label><input type="text" name="location" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control" required></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_session" class="btn btn-primary">Add Session</button>
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