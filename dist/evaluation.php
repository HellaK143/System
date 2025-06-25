<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_criteria'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    if ($name) {
        $stmt = $conn->prepare("INSERT INTO evaluation_criteria (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $desc);
        $stmt->execute();
        $stmt->close();
        header("Location: evaluation.php?added=1");
        exit();
    }
}
// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM evaluation_criteria WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: evaluation.php?deleted=1");
    exit();
}
// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_criteria'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    if ($name) {
        $stmt = $conn->prepare("UPDATE evaluation_criteria SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $desc, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: evaluation.php?updated=1");
        exit();
    }
}
// Fetch all criteria
$criteria = [];
$res = $conn->query("SELECT * FROM evaluation_criteria ORDER BY id ASC");
while ($row = $res->fetch_assoc()) $criteria[] = $row;
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Evaluation Criteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
        }
        .criteria-header {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 0.5px;
            color: #fff;
            background: linear-gradient(90deg, #1a1a1a 60%, #ff0000 100%);
            padding: 2rem 2rem 1.5rem 2rem;
            border-radius: 1rem 1rem 0 0;
            margin-bottom: 2rem;
        }
        .criteria-section {
            max-width: 900px;
            margin: 0 auto 2rem auto;
            padding: 2rem 2.5rem 2.5rem 2.5rem;
            background: #fff;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .pill-row {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
        }
        .pill-row .field-label {
            font-weight: 600;
            color: #555;
            min-width: 110px;
            max-width: 150px;
            flex: 0 0 120px;
        }
        .pill-row .field-value {
            color: #222;
            margin-left: 1.2rem;
            flex: 1 1 0%;
            word-break: break-word;
        }
        .criteria-actions .btn {
            min-width: 80px;
            margin-left: 0.5rem;
            margin-bottom: 0.5rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .criteria-actions .btn:hover {
            box-shadow: 0 2px 8px rgba(255,0,0,0.12);
            transform: translateY(-2px) scale(1.03);
        }
        .add-form-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .add-form-row input[type="text"] {
            flex: 1 1 0%;
        }
        @media (max-width: 767px) {
            .criteria-header { font-size: 1.3rem; padding: 1.2rem 0.7rem 1rem 0.7rem; }
            .criteria-section { max-width: 100%; padding: 1rem 0.5rem 1.5rem 0.5rem; }
            .pill-row { flex-direction: column; align-items: flex-start; gap: 0.2rem; padding: 0.7rem; }
            .pill-row .field-label { min-width: 0; max-width: 100%; flex: 1 1 0%; margin-bottom: 0.2rem; }
            .pill-row .field-value { margin-left: 0; }
            .add-form-row { flex-direction: column; gap: 0.5rem; }
        }
    </style>
</head>
<body>
<div class="criteria-header">
    <i class="fas fa-list-alt me-2"></i> Evaluation Criteria Management
</div>
<div class="criteria-section">
    <h4 class="mb-3">Add New Criteria</h4>
    <form method="post" class="add-form-row mb-4">
        <input type="hidden" name="add_criteria" value="1">
        <input type="text" class="form-control" name="name" placeholder="Criteria Name" required>
        <input type="text" class="form-control" name="description" placeholder="Description">
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
    </form>
    <h4 class="mb-3">Existing Criteria</h4>
    <?php foreach ($criteria as $c): ?>
        <div class="pill-row">
            <span class="field-label">#<?= $c['id'] ?></span>
            <span class="field-value">
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $c['id']): ?>
                    <form method="post" class="d-flex align-items-center gap-2 mb-0 flex-nowrap" style="flex-wrap:nowrap;">
                        <input type="hidden" name="edit_criteria" value="1">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" class="form-control form-control-sm" required>
                        <input type="text" name="description" value="<?= htmlspecialchars($c['description']) ?>" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-sm btn-primary" style="white-space:nowrap;">Save</button>
                        <a href="evaluation.php" class="btn btn-sm btn-secondary" style="white-space:nowrap;">Cancel</a>
                    </form>
                <?php else: ?>
                    <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                    <span class="text-muted small"><?= htmlspecialchars($c['description']) ?></span>
                <?php endif; ?>
            </span>
            <span class="criteria-actions ms-auto">
                <?php if (!isset($_GET['edit']) || $_GET['edit'] != $c['id']): ?>
                    <a href="evaluation.php?edit=<?= $c['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                    <a href="evaluation.php?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this criteria?');"><i class="fas fa-trash"></i> Delete</a>
                <?php endif; ?>
            </span>
        </div>
    <?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html> 