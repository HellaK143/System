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
<div class="container my-5 d-flex justify-content-center align-items-center" style="min-height:70vh;">
    <div class="card shadow-lg p-4" style="max-width: 420px; width:100%; border-radius: 18px;">
        <div class="text-center mb-4">
            <div style="width:80px;height:80px;margin:0 auto;">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['username'] ?? 'Evaluator') ?>&background=007bff&color=fff&rounded=true&size=80" alt="Avatar" class="rounded-circle shadow">
            </div>
            <h3 class="mt-3 mb-1" style="font-weight:700;">Edit Profile</h3>
            <div class="text-muted mb-2" style="font-size:1.08rem;">Update your evaluator account details</div>
        </div>
        <?php if ($success): ?><div class="alert alert-success text-center py-2"> <?= htmlspecialchars($success) ?> </div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger text-center py-2"> <?= htmlspecialchars($error) ?> </div><?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label fw-semibold">Name</label>
                <input type="text" name="username" class="form-control form-control-lg" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control form-control-lg" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="Leave blank to keep current password">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="font-size:1.1rem;">Save Changes</button>
        </form>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 