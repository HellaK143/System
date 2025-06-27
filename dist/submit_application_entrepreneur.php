<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entrepreneur') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$email = $_SESSION['email'];
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $file = '';
    if (!empty($_FILES['attachment']['name'])) {
        $target_dir = '../uploads/';
        $file_name = time() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $file = $file_name;
        } else {
            $error = 'File upload failed.';
        }
    }
    if (!$error && $category && $description) {
        $stmt = $conn->prepare("INSERT INTO applications (email, category, description, attachment, status, submitted_at) VALUES (?, ?, ?, ?, 'Submitted', NOW())");
        $stmt->bind_param('ssss', $email, $category, $description, $file);
        if ($stmt->execute()) {
            $success = 'Application submitted!';
            header('Location: my_applications_entrepreneur.php');
            exit;
        } else {
            $error = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } elseif (!$error) {
        $error = 'All fields are required.';
    }
}
$conn->close();
$page_title = 'Submit Application';
$breadcrumb_items = ['Submit Application'];
ob_start();
?>
<div class="container my-4" style="max-width:600px">
    <h3 class="mb-4">Submit New Application</h3>
    <?php if ($success): ?><div class="alert alert-success"> <?= htmlspecialchars($success) ?> </div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Attachment (optional)</label>
            <input type="file" name="attachment" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit Application</button>
        <a href="my_applications_entrepreneur.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
<?php
$page_content = ob_get_clean();
include 'template_entrepreneur.php'; 