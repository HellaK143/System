<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
$success = '';
$error = '';
$user_id = $_SESSION['user_id'];
// Handle photo removal
if (isset($_POST['remove_photo'])) {
    $conn->query("UPDATE users SET profile_picture=NULL WHERE user_id=$user_id");
    $_SESSION['profile_picture'] = null;
    $success = 'Profile photo removed.';
}
// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['remove_photo'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $profile_picture = null;
    // Handle photo upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $filename = 'admin_' . $user_id . '_' . time() . '.' . $ext;
        $target = __DIR__ . '/uploads/' . $filename;
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
            $profile_picture = $filename;
        } else {
            $error = 'Photo upload failed.';
        }
    }
    if ($username && $email) {
        if ($password) {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?" . ($profile_picture ? ", profile_picture=?" : "") . " WHERE user_id=?");
            if ($profile_picture) {
                $stmt->bind_param('ssssi', $username, $email, $password, $profile_picture, $user_id);
            } else {
                $stmt->bind_param('sssi', $username, $email, $password, $user_id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?" . ($profile_picture ? ", profile_picture=?" : "") . " WHERE user_id=?");
            if ($profile_picture) {
                $stmt->bind_param('sssi', $username, $email, $profile_picture, $user_id);
            } else {
                $stmt->bind_param('ssi', $username, $email, $user_id);
            }
        }
        if ($stmt->execute()) {
            $success = 'Profile updated successfully!';
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            if ($profile_picture) $_SESSION['profile_picture'] = $profile_picture;
        } else {
            $error = 'Update failed.';
        }
        $stmt->close();
    } else {
        $error = 'Username and email are required.';
    }
}
$res = $conn->query("SELECT username, email, profile_picture FROM users WHERE user_id=$user_id");
$profile = $res->fetch_assoc();
$conn->close();
$page_title = 'Admin Settings';
$breadcrumb_items = ['Settings'];
ob_start();
?>
<div class="container my-5" style="max-width:500px">
    <h2 class="mb-4">Edit Profile</h2>
    <div class="mb-3"><strong>Logged in as: <?= htmlspecialchars($profile['username']) ?></strong></div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="mb-3 text-center">
            <?php
            $img = $profile['profile_picture'] ? 'uploads/' . htmlspecialchars($profile['profile_picture']) : 'https://ui-avatars.com/api/?name=' . urlencode($profile['username']) . '&background=1a1a1a&color=fff&rounded=true&size=96';
            ?>
            <img src="<?= $img ?>" alt="Profile Photo" class="rounded-circle mb-2" style="width:96px;height:96px;object-fit:cover;">
            <div>
                <input type="file" name="profile_picture" accept="image/*" class="form-control mt-2">
                <?php if ($profile['profile_picture']): ?>
                    <button type="submit" name="remove_photo" class="btn btn-outline-danger btn-sm mt-2">Remove Photo</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($profile['username']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password <span class="text-muted small">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/template_admin.php'; 