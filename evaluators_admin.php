<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Filters
$name_filter = $_GET['name'] ?? '';
$email_filter = $_GET['email'] ?? '';
$status_filter = $_GET['status'] ?? '';
$where = [];
if ($name_filter) $where[] = "username LIKE '%" . $conn->real_escape_string($name_filter) . "%'";
if ($email_filter) $where[] = "email LIKE '%" . $conn->real_escape_string($email_filter) . "%'";
if ($status_filter) $where[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
$where_sql = $where ? ('WHERE role="evaluator" AND ' . implode(' AND ', $where)) : 'WHERE role="evaluator"';
$evaluators = $conn->query("SELECT user_id, username, email, status, created_at FROM users $where_sql ORDER BY username");
$conn->close();
$page_title = 'View Evaluators';
$breadcrumb_items = ['Evaluators', 'View Evaluators'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Evaluators</h2>
    <a href="export_evaluators.php" class="btn btn-success mb-3">Export as Excel</a>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <input type="text" class="form-control" name="name" placeholder="Search by Name" value="<?= htmlspecialchars($name_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="email" placeholder="Email" value="<?= htmlspecialchars($email_filter) ?>">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="status">
                <option value="">All Statuses</option>
                <option value="active"<?= $status_filter==='active'?' selected':'' ?>>Active</option>
                <option value="inactive"<?= $status_filter==='inactive'?' selected':'' ?>>Inactive</option>
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($evaluators && $evaluators->num_rows > 0): while($e = $evaluators->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($e['username']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= htmlspecialchars($e['status']) ?></td>
                    <td><?= htmlspecialchars($e['created_at']) ?></td>
                    <td>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $e['user_id'] ?>">Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $e['user_id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $e['user_id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="send_message.php">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($e['email']) ?>">
                              <input type="hidden" name="application_id" value="0">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $e['user_id'] ?>">Send Message to <?= htmlspecialchars($e['username']) ?></h5>
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
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-center text-muted">No evaluators found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 