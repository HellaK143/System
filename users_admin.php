<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');

// Handle search and filter
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$where = [];
if ($search) {
    $s = $conn->real_escape_string($search);
    $where[] = "(username LIKE '%$s%' OR email LIKE '%$s%')";
}
if ($role_filter) {
    $r = $conn->real_escape_string($role_filter);
    $where[] = "role = '$r'";
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$users = $conn->query("SELECT user_id, username, email, role FROM users $where_sql ORDER BY user_id DESC");
$roles = $conn->query("SELECT DISTINCT role FROM users");
$page_title = 'Manage Users';
$breadcrumb_items = ['Manage Users'];
ob_start();
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">System Users</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal"><i class="fas fa-user-plus me-1"></i> Create User</button>
    </div>
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <?php while($r = $roles->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($r['role']) ?>" <?= $role_filter===$r['role']?'selected':'' ?>><?= htmlspecialchars(ucfirst($r['role'])) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search"></i> Search</button>
        </div>
        <div class="col-md-2">
            <a href="users_admin.php" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($users->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>
            <?php else: while($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($u['user_id']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($u['role']) ?></span></td>
                    <td>
                        <a href="view_user.php?id=<?= $u['user_id'] ?>" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                        <a href="edit_user.php?id=<?= $u['user_id'] ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="delete_user.php?id=<?= $u['user_id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                        <a href="message_user.php?id=<?= $u['user_id'] ?>" class="btn btn-sm btn-secondary" title="Message"><i class="fas fa-envelope"></i></a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="create_user.php">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel"><i class="fas fa-user-plus me-2"></i> Create New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required>
            <option value="admin">Admin</option>
            <option value="mentor">Mentor</option>
            <option value="entrepreneur">Entrepreneur</option>
            <option value="evaluator">Evaluator</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Create User</button>
      </div>
    </form>
  </div>
</div>
<?php
$page_content = ob_get_clean();
include 'dist/template_admin.php'; 