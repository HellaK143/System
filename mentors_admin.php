<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Filters
$dept_filter = $_GET['department'] ?? '';
$expertise_filter = $_GET['expertise'] ?? '';
$name_filter = $_GET['name'] ?? '';
$where = [];
if ($dept_filter) $where[] = "assigned_department = '" . $conn->real_escape_string($dept_filter) . "'";
if ($expertise_filter) $where[] = "expertise_area LIKE '%" . $conn->real_escape_string($expertise_filter) . "%'";
if ($name_filter) $where[] = "full_name LIKE '%" . $conn->real_escape_string($name_filter) . "%'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$mentors = $conn->query("SELECT mentor_id, full_name, email, expertise_area, phone, assigned_department FROM mentors $where_sql ORDER BY full_name");
$conn->close();
$page_title = 'View Mentors';
$breadcrumb_items = ['Mentors', 'View Mentors'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Mentors</h2>
    <a href="export_mentors.php" class="btn btn-success mb-3">Export as Excel</a>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <input type="text" class="form-control" name="name" placeholder="Search by Name" value="<?= htmlspecialchars($name_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="expertise" placeholder="Expertise Area" value="<?= htmlspecialchars($expertise_filter) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="department" placeholder="Department" value="<?= htmlspecialchars($dept_filter) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Expertise Area</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($mentors && $mentors->num_rows > 0): while($m = $mentors->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($m['full_name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['expertise_area']) ?></td>
                    <td><?= htmlspecialchars($m['phone']) ?></td>
                    <td><?= htmlspecialchars($m['assigned_department']) ?></td>
                    <td>
                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $m['mentor_id'] ?>">Message</button>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $m['mentor_id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $m['mentor_id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post" action="send_message.php">
                              <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($m['email']) ?>">
                              <input type="hidden" name="application_id" value="0">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $m['mentor_id'] ?>">Send Message to <?= htmlspecialchars($m['full_name']) ?></h5>
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
                <tr><td colspan="6" class="text-center text-muted">No mentors found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 