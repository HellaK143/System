<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$id = intval($_GET['id'] ?? 0);
if (!$id) die('Invalid user ID');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$username || !$email || !$role) {
        $error = 'Missing fields';
    } else {
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $role = $conn->real_escape_string($role);
        $set = "username='$username', email='$email', role='$role'";
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $set .= ", password='$password_hash'";
        }
        $sql = "UPDATE users SET $set WHERE user_id=$id";
        if ($conn->query($sql)) {
            header('Location: users_admin.php?success=User+updated'); exit;
        } else {
            $error = 'DB error';
        }
    }
}

$user = $conn->query("SELECT * FROM users WHERE user_id=$id")->fetch_assoc();
if (!$user) die('User not found');

$page_title = 'Edit User - ' . $user['username'];
$breadcrumb_items = ['Manage Users', $user['username'], 'Edit'];

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit User Profile</h5>
            </div>
            <div class="card-body p-4">
                <!-- User Profile Header -->
                <div class="text-center mb-4">
                    <div class="user-avatar mb-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="mb-2"><?= htmlspecialchars($user['username']) ?></h4>
                    <span class="badge bg-primary fs-6 mb-3"><?= htmlspecialchars(ucfirst($user['role'])) ?></span>
                    <p class="text-muted mb-0">User ID: <?= htmlspecialchars($user['user_id']) ?></p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2 text-primary"></i>Username
                            </label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope me-2 text-primary"></i>Email
                            </label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user-tag me-2 text-primary"></i>Role
                            </label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
                                <option value="mentor" <?= $user['role']==='mentor'?'selected':'' ?>>Mentor</option>
                                <option value="entrepreneur" <?= $user['role']==='entrepreneur'?'selected':'' ?>>Entrepreneur</option>
                                <option value="evaluator" <?= $user['role']==='evaluator'?'selected':'' ?>>Evaluator</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-lock me-2 text-primary"></i>Password
                            </label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Leave blank to keep unchanged">
                            <small class="text-muted">Only fill this if you want to change the password</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="users_admin.php" class="btn btn-secondary btn-custom">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// Additional CSS for custom styling
$additional_css = "
<style>
.user-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto;
}
.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
.btn-custom {
    border-radius: 10px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.card {
    transition: all 0.3s ease;
    border-radius: 15px;
}
.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
</style>
";

include 'dist/template_admin.php';
?> 