<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Filters
$name_filter = $_GET['name'] ?? '';
$email_filter = $_GET['email'] ?? '';
$department_filter = $_GET['department'] ?? '';
$sector_filter = $_GET['sector'] ?? '';
$where = [];
if ($name_filter) $where[] = "CONCAT(first_name, ' ', last_name) LIKE '%" . $conn->real_escape_string($name_filter) . "%'";
if ($email_filter) $where[] = "email LIKE '%" . $conn->real_escape_string($email_filter) . "%'";
if ($department_filter) $where[] = "department LIKE '%" . $conn->real_escape_string($department_filter) . "%'";
if ($sector_filter) $where[] = "sector LIKE '%" . $conn->real_escape_string($sector_filter) . "%'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$entrepreneurs = $conn->query("SELECT entrepreneur_id, first_name, last_name, email, department, sector, phone FROM entrepreneur $where_sql ORDER BY first_name, last_name");
$conn->close();
$page_title = 'View Entrepreneurs';
$breadcrumb_items = ['Entrepreneurs', 'View Entrepreneurs'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Entrepreneurs</h2>
    <a href="export_entrepreneurs.php" class="btn btn-success mb-3">Export as Excel</a>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <input type="text" class="form-control" name="name" placeholder="Search by Name" value="<?= htmlspecialchars($name_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="email" placeholder="Email" value="<?= htmlspecialchars($email_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="department" placeholder="Department" value="<?= htmlspecialchars($department_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="sector" placeholder="Sector" value="<?= htmlspecialchars($sector_filter) ?>">
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
                    <th>Department</th>
                    <th>Sector</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entrepreneurs && $entrepreneurs->num_rows > 0): while($e = $entrepreneurs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= htmlspecialchars($e['department']) ?></td>
                    <td><?= htmlspecialchars($e['sector']) ?></td>
                    <td><?= htmlspecialchars($e['phone']) ?></td>
                    <td>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $e['entrepreneur_id'] ?>">Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $e['entrepreneur_id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $e['entrepreneur_id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="send_message.php">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($e['email']) ?>">
                              <input type="hidden" name="application_id" value="0">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $e['entrepreneur_id'] ?>">Send Message to <?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></h5>
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
                <tr><td colspan="6" class="text-center text-muted">No entrepreneurs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 