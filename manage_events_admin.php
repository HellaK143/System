<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = trim($_POST['title']);
    $type = trim($_POST['event_type']);
    $start = trim($_POST['start_datetime']);
    $end = trim($_POST['end_datetime']);
    $location = trim($_POST['location']);
    $stmt = $conn->prepare("INSERT INTO events (title, event_type, start_datetime, end_datetime, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $title, $type, $start, $end, $location);
    $stmt->execute();
    if ($stmt->execute()) {
        require_once 'db.php';
        log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Event Modified', 'Event added/edited/deleted by admin.');
    }
    $stmt->close();
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'event', 'Event Added', 'A new event ($title) of type $type was scheduled for $start at $location.');
    }
    header('Location: manage_events_admin.php'); exit;
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_event'])) {
    $id = intval($_POST['event_id']);
    $title = trim($_POST['title']);
    $type = trim($_POST['event_type']);
    $start = trim($_POST['start_datetime']);
    $end = trim($_POST['end_datetime']);
    $location = trim($_POST['location']);
    $stmt = $conn->prepare("UPDATE events SET title=?, event_type=?, start_datetime=?, end_datetime=?, location=? WHERE id=?");
    $stmt->bind_param('sssssi', $title, $type, $start, $end, $location, $id);
    $stmt->execute();
    if ($stmt->execute()) {
        require_once 'db.php';
        log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Event Modified', 'Event added/edited/deleted by admin.');
    }
    $stmt->close();
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'event', 'Event Edited', 'Event #'.$id.' ($title) was updated.');
    }
    header('Location: manage_events_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event'])) {
    $id = intval($_POST['event_id']);
    $conn->query("DELETE FROM events WHERE id=$id");
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'error', 'event', 'Event Deleted', 'Event #'.$id.' was deleted.');
    }
    header('Location: manage_events_admin.php'); exit;
}
$events = $conn->query("SELECT id, title, event_type, start_datetime, end_datetime, location FROM events ORDER BY start_datetime DESC");
$conn->close();
$page_title = 'Manage Events';
$breadcrumb_items = ['Events', 'Manage Events'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Events</h2>
    <a href="export_events.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addEventModal">Add New Event</button>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($events && $events->num_rows > 0): while($e = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($e['id']) ?></td>
                    <td><?= htmlspecialchars($e['title']) ?></td>
                    <td><?= htmlspecialchars($e['event_type']) ?></td>
                    <td><?= htmlspecialchars($e['start_datetime']) ?></td>
                    <td><?= htmlspecialchars($e['end_datetime']) ?></td>
                    <td><?= htmlspecialchars($e['location']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editEventModal<?= $e['id'] ?>">Edit</button>
                        <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this event?');">
                            <input type="hidden" name="event_id" value="<?= $e['id'] ?>">
                            <button type="submit" name="delete_event" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editEventModal<?= $e['id'] ?>" tabindex="-1" aria-labelledby="editEventModalLabel<?= $e['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editEventModalLabel<?= $e['id'] ?>">Edit Event</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="event_id" value="<?= $e['id'] ?>">
                          <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($e['title']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" name="event_type" class="form-control" value="<?= htmlspecialchars($e['event_type']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Start</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($e['start_datetime'])) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">End</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($e['end_datetime'])) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($e['location']) ?>" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" name="edit_event" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center text-muted">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <input type="text" name="event_type" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Start</label>
            <input type="datetime-local" name="start_datetime" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">End</label>
            <input type="datetime-local" name="end_datetime" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 