<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$allowed_types = [
    'pdf' => 'PDF',
    'doc' => 'Word',
    'docx' => 'Word',
    'ppt' => 'PowerPoint',
    'pptx' => 'PowerPoint',
    'xls' => 'Excel',
    'xlsx' => 'Excel',
    'jpg' => 'Image',
    'jpeg' => 'Image',
    'png' => 'Image',
    'gif' => 'Image',
    'mp4' => 'Video',
    'avi' => 'Video',
    'mov' => 'Video',
    'txt' => 'Text',
    'zip' => 'Archive',
    'rar' => 'Archive',
    'csv' => 'CSV',
    // add more as needed
];

$application_id = isset($_GET['application_id']) ? intval($_GET['application_id']) : (isset($_POST['application_id']) ? intval($_POST['application_id']) : 0);
if (!$application_id) {
    die('<div class="alert alert-danger m-5">No application specified.</div>');
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['attachment'])) {
    $file = $_FILES['attachment'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed_types)) {
            $msg = '<div class="alert alert-danger">File type not allowed.</div>';
        } else {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $new_name = uniqid('att_') . '.' . $ext;
            $target = $upload_dir . $new_name;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $file_type = $allowed_types[$ext];
                $stmt = $conn->prepare("INSERT INTO attachments (application_id, file_type, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $application_id, $file_type, $new_name);
                $stmt->execute();
                $stmt->close();
                $msg = '<div class="alert alert-success">File uploaded successfully.</div>';
            } else {
                $msg = '<div class="alert alert-danger">Failed to upload file.</div>';
            }
        }
    } else {
        $msg = '<div class="alert alert-danger">No file selected or upload error.</div>';
    }
}

// Fetch attachments for this application
$stmt = $conn->prepare("SELECT id, file_type, file_path, uploaded_at FROM attachments WHERE application_id = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$attachments = $stmt->get_result();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attachments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Attachments</h2>
    <?= $msg ?>
    <form method="post" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="application_id" value="<?= $application_id ?>">
        <div class="mb-3">
            <label for="attachment" class="form-label">Upload Attachment</label>
            <input type="file" class="form-control" id="attachment" name="attachment" required>
            <div class="form-text">Allowed types: <?= implode(', ', array_keys($allowed_types)) ?></div>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    <h5>Existing Attachments</h5>
    <?php if ($attachments->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($att = $attachments->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-paperclip"></i> <?= htmlspecialchars($att['file_type']) ?> - <?= htmlspecialchars($att['file_path']) ?> <small class="text-muted ms-2">(<?= $att['uploaded_at'] ?>)</small></span>
                    <a href="../uploads/<?= urlencode($att['file_path']) ?>" class="btn btn-sm btn-success" download>Download</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">No attachments uploaded yet.</div>
    <?php endif; ?>
    <a href="application_view.php?id=<?= $application_id ?>" class="btn btn-secondary mt-4">Back to Application</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html> 