<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    die('<div class="alert alert-danger m-5">Invalid application ID.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM applications WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: applications.php?deleted=1");
        exit();
    } else {
        $error = "Error deleting application: " . $conn->error;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
    <style>
        .delete-card { max-width: 500px; margin: 4rem auto; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card delete-card shadow">
        <div class="card-header bg-danger text-white">
            <i class="fas fa-trash"></i> Delete Application
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"> <?= $error ?> </div>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <p class="mb-4">Are you sure you want to delete application #<?= htmlspecialchars($id) ?>? This action cannot be undone.</p>
                    <div class="text-end">
                        <a href="application_view.php?id=<?= htmlspecialchars($id) ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html> 