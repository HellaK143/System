<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Filters
$user_filter = $_GET['user'] ?? '';
$resource_filter = $_GET['resource'] ?? '';
$date_filter = $_GET['date'] ?? '';
$status_filter = $_GET['status'] ?? '';
$where = [];
if ($user_filter) $where[] = "user_id = '" . $conn->real_escape_string($user_filter) . "'";
if ($resource_filter) $where[] = "resource_id = '" . $conn->real_escape_string($resource_filter) . "'";
if ($date_filter) $where[] = "DATE(start_datetime) = '" . $conn->real_escape_string($date_filter) . "'";
if ($status_filter) $where[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$bookings = $conn->query("SELECT resource_id, user_id, start_datetime, end_datetime, status FROM bookings $where_sql ORDER BY start_datetime DESC");
// Fetch users for messaging
$users = $conn->query("SELECT user_id, username, email FROM users");
$user_map = [];
if ($users) while($u = $users->fetch_assoc()) $user_map[$u['user_id']] = $u;
$conn->close();
$page_title = 'View Bookings';
$breadcrumb_items = ['Bookings', 'View Bookings'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Bookings</h2>
    <a href="export_bookings.php" class="btn btn-success mb-3">Export as Excel</a>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <input type="text" class="form-control" name="user" placeholder="User ID" value="<?= htmlspecialchars($user_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="resource" placeholder="Resource ID" value="<?= htmlspecialchars($resource_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date_filter) ?>">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="status">
                <option value="">All Statuses</option>
                <option value="pending"<?= $status_filter==='pending'?' selected':'' ?>>Pending</option>
                <option value="approved"<?= $status_filter==='approved'?' selected':'' ?>>Approved</option>
                <option value="rejected"<?= $status_filter==='rejected'?' selected':'' ?>>Rejected</option>
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
                    <th>Resource</th>
                    <th>User</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookings && $bookings->num_rows > 0): while($b = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($b['resource_id']) ?></td>
                    <td><?= isset($user_map[$b['user_id']]) ? htmlspecialchars($user_map[$b['user_id']]['username']) : htmlspecialchars($b['user_id']) ?></td>
                    <td><?= htmlspecialchars($b['start_datetime']) ?></td>
                    <td><?= htmlspecialchars($b['end_datetime']) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                    <td>
                        <?php if (isset($user_map[$b['user_id']])): ?>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $b['user_id'] ?>">Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $b['user_id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $b['user_id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="send_message.php">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($user_map[$b['user_id']]['email']) ?>">
                              <input type="hidden" name="application_id" value="0">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $b['user_id'] ?>">Send Message to <?= htmlspecialchars($user_map[$b['user_id']]['username']) ?></h5>
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
                <tr><td colspan="6" class="text-center text-muted">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 