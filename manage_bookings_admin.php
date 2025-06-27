<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Fetch users and resources for dropdowns
$users_res = $conn->query("SELECT user_id, username FROM users ORDER BY username");
$resources_res = $conn->query("SELECT id, name FROM resources ORDER BY name");
$users_list = [];
$resources_list = [];
if ($users_res) while ($u = $users_res->fetch_assoc()) $users_list[] = $u;
if ($resources_res) while ($r = $resources_res->fetch_assoc()) $resources_list[] = $r;
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_booking'])) {
    echo '<pre>DEBUG POST: ' . print_r($_POST, true) . '</pre>';
    $resource = trim($_POST['resource_id']);
    $user = trim($_POST['user_id']);
    $start = trim($_POST['start_datetime']);
    $end = trim($_POST['end_datetime']);
    $status = trim($_POST['status']);
    $stmt = $conn->prepare("INSERT INTO bookings (resource_id, user_id, start_datetime, end_datetime, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iisss', $resource, $user, $start, $end, $status);
    if (!$stmt->execute()) {
        echo '<div class="alert alert-danger">Insert error: ' . htmlspecialchars($stmt->error) . '</div>';
    } else {
        $stmt->close();
        // Notify all admins
        foreach (get_user_ids_by_role('admin') as $admin_id) {
            add_notification($admin_id, 'info', 'booking', 'Booking Added', 'A new booking was added for resource ' . $resource . ' by user ' . $user);
        }
        // Notify user
        add_notification($user, 'info', 'booking', 'Booking Created', 'Your booking for resource ' . $resource . ' has been created.');
        header('Location: manage_bookings_admin.php'); exit;
    }
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_booking'])) {
    $id = intval($_POST['booking_id']);
    $resource = trim($_POST['resource_id']);
    $user = trim($_POST['user_id']);
    $start = trim($_POST['start_datetime']);
    $end = trim($_POST['end_datetime']);
    $status = trim($_POST['status']);
    $stmt = $conn->prepare("UPDATE bookings SET resource_id=?, user_id=?, start_datetime=?, end_datetime=?, status=? WHERE id=?");
    $stmt->bind_param('sssssi', $resource, $user, $start, $end, $status, $id);
    $stmt->execute();
    $stmt->close();
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'booking', 'Booking Edited', 'Booking #' . $id . ' was edited.');
    }
    // Notify user
    add_notification($user, 'info', 'booking', 'Booking Updated', 'Your booking #' . $id . ' has been updated.');
    header('Location: manage_bookings_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    $id = intval($_POST['booking_id']);
    // Get user_id before delete
    $user = null;
    $res = $conn->query("SELECT user_id FROM bookings WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) $user = $row['user_id'];
    $conn->query("DELETE FROM bookings WHERE id=$id");
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'error', 'booking', 'Booking Deleted', 'Booking #' . $id . ' was deleted.');
    }
    // Notify user
    if ($user) add_notification($user, 'error', 'booking', 'Booking Deleted', 'Your booking #' . $id . ' was deleted.');
    header('Location: manage_bookings_admin.php'); exit;
}
$bookings = $conn->query("SELECT id, resource_id, user_id, start_datetime, end_datetime, status FROM bookings ORDER BY start_datetime DESC");
$conn->close();
$page_title = 'Manage Bookings';
$breadcrumb_items = ['Bookings', 'Manage Bookings'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Bookings</h2>
    <a href="export_bookings.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addBookingModal">Add New Booking</button>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search bookings...">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Resource</th>
                    <th>User</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Resource"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="User"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Start"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="End"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Status"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookings && $bookings->num_rows > 0): while($b = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($b['id']) ?></td>
                    <td><?= htmlspecialchars($b['resource_id']) ?></td>
                    <td><?= htmlspecialchars($b['user_id']) ?></td>
                    <td><?= htmlspecialchars($b['start_datetime']) ?></td>
                    <td><?= htmlspecialchars($b['end_datetime']) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editBookingModal<?= $b['id'] ?>">Edit</button>
                        <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this booking?');">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <button type="submit" name="delete_booking" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editBookingModal<?= $b['id'] ?>" tabindex="-1" aria-labelledby="editBookingModalLabel<?= $b['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editBookingModalLabel<?= $b['id'] ?>">Edit Booking</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                          <div class="mb-3"><label class="form-label">Resource</label><select name="resource_id" class="form-select" required>
                            <option value="">Select Resource</option>
                            <?php foreach ($resources_list as $r): ?>
                              <option value="<?= $r['id'] ?>" <?= $b['resource_id'] == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?> (ID: <?= $r['id'] ?>)</option>
                            <?php endforeach; ?>
                          </select></div>
                          <div class="mb-3"><label class="form-label">User</label><select name="user_id" class="form-select" required>
                            <option value="">Select User</option>
                            <?php foreach ($users_list as $u): ?>
                              <option value="<?= $u['user_id'] ?>" <?= $b['user_id'] == $u['user_id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?> (ID: <?= $u['user_id'] ?>)</option>
                            <?php endforeach; ?>
                          </select></div>
                          <div class="mb-3"><label class="form-label">Start</label><input type="datetime-local" name="start_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($b['start_datetime'])) ?>" required></div>
                          <div class="mb-3"><label class="form-label">End</label><input type="datetime-local" name="end_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($b['end_datetime'])) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control" value="<?= htmlspecialchars($b['status']) ?>" required></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" name="edit_booking" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center text-muted">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1" aria-labelledby="addBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addBookingModalLabel">Add New Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Resource</label>
            <select name="resource_id" class="form-select" required>
              <option value="">Select Resource</option>
              <?php foreach ($resources_list as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?> (ID: <?= $r['id'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" class="form-select" required>
              <option value="">Select User</option>
              <?php foreach ($users_list as $u): ?>
                <option value="<?= $u['user_id'] ?>"><?= htmlspecialchars($u['username']) ?> (ID: <?= $u['user_id'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Start</label><input type="datetime-local" name="start_datetime" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">End</label><input type="datetime-local" name="end_datetime" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Status</label><input type="text" name="status" class="form-control" required></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_booking" class="btn btn-primary">Add Booking</button>
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