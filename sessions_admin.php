<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Filters
$type_filter = $_GET['type'] ?? '';
$date_filter = $_GET['date'] ?? '';
$status_filter = $_GET['status'] ?? '';
$where = [];
if ($type_filter) $where[] = "session_type = '" . $conn->real_escape_string($type_filter) . "'";
if ($date_filter) $where[] = "DATE(date) = '" . $conn->real_escape_string($date_filter) . "'";
if ($status_filter) $where[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sessions = $conn->query("SELECT id, session_type, date, time, location, status, scheduled_for FROM sessions $where_sql ORDER BY date DESC, time DESC");
// Fetch users for messaging
$users = $conn->query("SELECT user_id, username, email FROM users");
$user_map = [];
if ($users) while($u = $users->fetch_assoc()) $user_map[$u['user_id']] = $u;
$conn->close();
$page_title = 'View Sessions';
$breadcrumb_items = ['Sessions', 'View Sessions'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Sessions</h2>
    <a href="export_sessions.php" class="btn btn-success mb-3">Export as Excel</a>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <input type="text" class="form-control" name="type" placeholder="Session Type" value="<?= htmlspecialchars($type_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date_filter) ?>">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="status">
                <option value="">All Statuses</option>
                <option value="scheduled"<?= $status_filter==='scheduled'?' selected':'' ?>>Scheduled</option>
                <option value="completed"<?= $status_filter==='completed'?' selected':'' ?>>Completed</option>
                <option value="cancelled"<?= $status_filter==='cancelled'?' selected':'' ?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sessions && $sessions->num_rows > 0): while($s = $sessions->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($s['session_type']) ?></td>
                    <td><?= htmlspecialchars($s['date']) ?></td>
                    <td><?= htmlspecialchars($s['time']) ?></td>
                    <td><?= htmlspecialchars($s['location']) ?></td>
                    <td><?= htmlspecialchars($s['status']) ?></td>
                    <td><?= isset($user_map[$s['scheduled_for']]) ? htmlspecialchars($user_map[$s['scheduled_for']]['username']) : htmlspecialchars($s['scheduled_for']) ?></td>
                    <td>
                        <?php if (isset($user_map[$s['scheduled_for']])): ?>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $s['id'] ?>">Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $s['id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $s['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="send_message.php">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($user_map[$s['scheduled_for']]['email']) ?>">
                              <input type="hidden" name="application_id" value="0">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $s['id'] ?>">Send Message to <?= htmlspecialchars($user_map[$s['scheduled_for']]['username']) ?></h5>
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
                <tr><td colspan="7" class="text-center text-muted">No sessions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 