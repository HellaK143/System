<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$evaluator_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
if (!$evaluator_id || $role !== 'evaluator') die('Access denied.');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die('Invalid application ID.');
$app_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM applications WHERE id = ? AND assigned_evaluator = ?");
$stmt->bind_param("ii", $app_id, $evaluator_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die('Application not found or not assigned to you.');
$app = $res->fetch_assoc();
$stmt->close();
$conn->close();
$page_title = 'View Application';
$breadcrumb_items = ['View Application'];
ob_start();
?>
<div class="container my-5">
    <h2>Application Details</h2>
    <table class="table table-bordered">
        <tr><th>ID</th><td><?= $app['id'] ?></td></tr>
        <tr><th>Applicant Name</th><td><?= htmlspecialchars($app['full_name']) ?></td></tr>
        <tr><th>Program</th><td><?= htmlspecialchars($app['program']) ?></td></tr>
        <tr><th>Category</th><td><?= htmlspecialchars($app['category']) ?></td></tr>
        <tr><th>Business Idea</th><td><?= htmlspecialchars($app['business_idea_name']) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($app['status']) ?></td></tr>
        <tr><th>Submitted At</th><td><?= htmlspecialchars($app['submitted_at']) ?></td></tr>
        <tr><th>Description</th><td><?= nl2br(htmlspecialchars($app['description'])) ?></td></tr>
    </table>
    <a href="evaluator_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 