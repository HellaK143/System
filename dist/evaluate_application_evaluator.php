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
    header("Location: evaluate_application_evaluator.php?id=$app_id&saved=1");
    exit();
}
$conn->close();
$page_title = 'Evaluate Application';
$breadcrumb_items = ['Evaluate Application'];
$additional_css = 'body { background: #f6f7fb; }';
ob_start();
?>
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
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 