<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Filters
$type_filter = $_GET['type'] ?? '';
$date_filter = $_GET['date'] ?? '';
$location_filter = $_GET['location'] ?? '';
$where = [];
if ($type_filter) $where[] = "event_type = '" . $conn->real_escape_string($type_filter) . "'";
if ($date_filter) $where[] = "DATE(start_datetime) = '" . $conn->real_escape_string($date_filter) . "'";
if ($location_filter) $where[] = "location LIKE '%" . $conn->real_escape_string($location_filter) . "%'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$events = $conn->query("SELECT id, title, event_type, start_datetime, end_datetime, location, created_by FROM events $where_sql ORDER BY start_datetime DESC");
// Fetch users for messaging
$users = $conn->query("SELECT user_id, username, email FROM users");
$user_map = [];
if ($users) while($u = $users->fetch_assoc()) $user_map[$u['user_id']] = $u;
$conn->close();
$page_title = 'View Events';
$breadcrumb_items = ['Events', 'View Events'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Events</h2>
    <a href="export_events.php" class="btn btn-success mb-3">Export as Excel</a>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <input type="text" class="form-control" name="type" placeholder="Event Type" value="<?= htmlspecialchars($type_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="location" placeholder="Location" value="<?= htmlspecialchars($location_filter) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-light">
                <tr>
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
                    <td><?= htmlspecialchars($e['title']) ?></td>
                    <td><?= htmlspecialchars($e['event_type']) ?></td>
                    <td><?= htmlspecialchars($e['start_datetime']) ?></td>
                    <td><?= htmlspecialchars($e['end_datetime']) ?></td>
                    <td><?= htmlspecialchars($e['location']) ?></td>
                    <td>
                        <?php if (isset($user_map[$e['created_by']])): ?>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $e['id'] ?>">Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $e['id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $e['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="send_message.php">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($user_map[$e['created_by']]['email']) ?>">
                              <input type="hidden" name="application_id" value="0">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $e['id'] ?>">Send Message to <?= htmlspecialchars($user_map[$e['created_by']]['username']) ?></h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <textarea name="message" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" name="send_message" class="btn btn-primary">Send</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" class="text-center text-muted">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 