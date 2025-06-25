<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get evaluator id from session
$evaluator_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
if (!$evaluator_id || $role !== 'evaluator') {
    die('<div class="alert alert-danger m-5">Access denied. Please log in as an evaluator.</div>');
}

// Get application ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger m-5">Invalid application ID.</div>');
}
$app_id = intval($_GET['id']);

// Check if this evaluator is assigned to this application
$stmt = $conn->prepare("SELECT assigned_evaluator FROM applications WHERE id = ?");
$stmt->bind_param("i", $app_id);
$stmt->execute();
$stmt->bind_result($assigned_evaluator);
$stmt->fetch();
$stmt->close();
if (!$assigned_evaluator || $assigned_evaluator != $evaluator_id) {
    $conn->close();
    die('<div class="alert alert-danger m-5">You are not assigned to evaluate this application.</div>');
}

// Fetch application summary
$app = null;
$stmt = $conn->prepare("SELECT id, full_name, program, category, business_idea_name, status FROM applications WHERE id = ?");
$stmt->bind_param("i", $app_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die('<div class="alert alert-warning m-5">Application not found.</div>');
}
$app = $res->fetch_assoc();
$stmt->close();

// Fetch criteria
$criteria = [];
$res = $conn->query("SELECT * FROM evaluation_criteria ORDER BY id ASC");
while ($row = $res->fetch_assoc()) $criteria[] = $row;

// Fetch existing scores for this evaluator/application
$scores = [];
$stmt = $conn->prepare("SELECT criteria_id, score, comment FROM evaluation_scores WHERE application_id = ? AND evaluator_id = ?");
$stmt->bind_param("ii", $app_id, $evaluator_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $scores[$row['criteria_id']] = $row;
$stmt->close();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scores'])) {
    foreach ($_POST['scores'] as $crit_id => $score) {
        $score_val = intval($score['score']);
        $comment = trim($score['comment']);
        // Check if score exists
        if (isset($scores[$crit_id])) {
            $stmt = $conn->prepare("UPDATE evaluation_scores SET score = ?, comment = ? WHERE application_id = ? AND evaluator_id = ? AND criteria_id = ?");
            $stmt->bind_param("isiii", $score_val, $comment, $app_id, $evaluator_id, $crit_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO evaluation_scores (application_id, evaluator_id, criteria_id, score, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiis", $app_id, $evaluator_id, $crit_id, $score_val, $comment);
        }
        $stmt->execute();
        $stmt->close();
    }
    header("Location: evaluate_application.php?id=$app_id&saved=1");
    exit();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
    <style>
        body { background: #f6f7fb; }
        .eval-header {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 0.5px;
            color: #fff;
            background: linear-gradient(90deg, #1a1a1a 60%, #ff0000 100%);
            padding: 2rem 2rem 1.5rem 2rem;
            border-radius: 1rem 1rem 0 0;
            margin-bottom: 2rem;
        }
        .eval-section {
            max-width: 900px;
            margin: 0 auto 2rem auto;
            padding: 2rem 2.5rem 2.5rem 2.5rem;
            background: #fff;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .app-summary {
            margin-bottom: 2rem;
        }
        .app-summary .row {
            margin-bottom: 0.5rem;
        }
        .score-row {
            display: flex;
            align-items: flex-start;
            background: #f8f9fa;
            border-radius: 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 1rem 1.5rem;
            margin-bottom: 1.2rem;
            gap: 1.5rem;
        }
        .score-row .criteria-label {
            font-weight: 600;
            color: #555;
            min-width: 160px;
            max-width: 220px;
            flex: 0 0 180px;
        }
        .score-row .score-input {
            width: 80px;
        }
        .score-row .comment-input {
            flex: 1 1 0%;
        }
        .score-row .score-range {
            font-size: 0.95rem;
            color: #888;
            margin-left: 0.5rem;
        }
        @media (max-width: 767px) {
            .eval-header { font-size: 1.3rem; padding: 1.2rem 0.7rem 1rem 0.7rem; }
            .eval-section { max-width: 100%; padding: 1rem 0.5rem 1.5rem 0.5rem; }
            .score-row { flex-direction: column; gap: 0.7rem; padding: 0.7rem; }
            .score-row .criteria-label { min-width: 0; max-width: 100%; flex: 1 1 0%; margin-bottom: 0.2rem; }
            .score-row .score-input { width: 100%; }
        }
    </style>
</head>
<body>
<div class="eval-header">
    <i class="fas fa-clipboard-check me-2"></i> Evaluate Application #<?= $app['id'] ?>
</div>
<div class="eval-section">
    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success">Scores saved successfully!</div>
    <?php endif; ?>
    <div class="app-summary">
        <div class="row">
            <div class="col-md-4 fw-bold text-secondary">Applicant:</div>
            <div class="col-md-8 text-dark"><?= htmlspecialchars($app['full_name']) ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 fw-bold text-secondary">Program:</div>
            <div class="col-md-8 text-dark"><?= htmlspecialchars($app['program']) ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 fw-bold text-secondary">Category:</div>
            <div class="col-md-8 text-dark"><?= htmlspecialchars($app['category']) ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 fw-bold text-secondary">Business Idea:</div>
            <div class="col-md-8 text-dark"><?= htmlspecialchars($app['business_idea_name']) ?></div>
        </div>
        <div class="row">
            <div class="col-md-4 fw-bold text-secondary">Status:</div>
            <div class="col-md-8 text-dark"><span class="badge bg-info text-dark"><?= htmlspecialchars($app['status']) ?></span></div>
        </div>
    </div>
    <form method="post">
        <h5 class="mb-3">Evaluation Criteria</h5>
        <?php foreach ($criteria as $c): $cid = $c['id']; ?>
            <div class="score-row">
                <div class="criteria-label">
                    <?= htmlspecialchars($c['name']) ?><br>
                    <span class="text-muted small"><?= htmlspecialchars($c['description']) ?></span>
                </div>
                <div>
                    <input type="number" min="1" max="10" class="form-control score-input" name="scores[<?= $cid ?>][score]" value="<?= isset($scores[$cid]) ? htmlspecialchars($scores[$cid]['score']) : '' ?>" required>
                    <span class="score-range">(1-10)</span>
                </div>
                <div class="flex-grow-1">
                    <input type="text" class="form-control comment-input" name="scores[<?= $cid ?>][comment]" placeholder="Comment (optional)" value="<?= isset($scores[$cid]) ? htmlspecialchars($scores[$cid]['comment']) : '' ?>">
                </div>
            </div>
        <?php endforeach; ?>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success btn-lg px-4"><i class="fas fa-save"></i> Save Scores</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html> 