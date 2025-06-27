<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'evaluator') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$user_id = $_SESSION['user_id'];
$success = '';
$error = '';
// Fetch evaluator info
$res = $conn->query("SELECT username, email FROM users WHERE user_id = $user_id");
$user = $res ? $res->fetch_assoc() : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if ($username && $email) {
        $sql = "UPDATE users SET username=?, email=?";
        $params = [$username, $email];
        $types = 'ss';
        if ($password) {
            $sql .= ", password=?";
            $params[] = $password;
            $types .= 's';
        }
        $sql .= " WHERE user_id=?";
        $params[] = $user_id;
        $types .= 'i';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
        header('Location: settings_evaluator.php?success=1');
        exit;
    } else {
        $error = 'Name and email are required.';
    }
}
if (isset($_GET['success'])) {
    $success = 'Profile updated.';
}
$page_title = 'Evaluator Settings';
$breadcrumb_items = ['Settings'];
ob_start();
?>
<div class="container my-5">
    <h2>Edit Profile</h2>
    <?php if ($success): ?><div class="alert alert-success"> <?= htmlspecialchars($success) ?> </div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div><?php endif; ?>
    <form method="post" class="w-50">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 