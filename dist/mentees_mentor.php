<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') die('Access denied.');
require_once '../db.php';
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');

// Try to find mentor_id by user_id
$mentor_id = null;
$mentor_res = $conn->query("SELECT mentor_id FROM mentors WHERE user_id = $user_id");
if ($mentor_res && $m = $mentor_res->fetch_assoc()) {
    $mentor_id = $m['mentor_id'];
} else {
    // Fallback: try to match by email
    $user_email = '';
    $user_res = $conn->query("SELECT email FROM users WHERE user_id = $user_id");
    if ($user_res && $u = $user_res->fetch_assoc()) {
        $user_email = strtolower(trim($u['email']));
        $mentor_res2 = $conn->query("SELECT mentor_id FROM mentors WHERE LOWER(TRIM(email)) = '" . $conn->real_escape_string($user_email) . "'");
        if ($mentor_res2 && $m2 = $mentor_res2->fetch_assoc()) {
            $mentor_id = $m2['mentor_id'];
        }
    }
}

if (!$mentor_id) {
    echo "<div class='alert alert-warning'>You are not yet registered as a mentor in the system. Please contact the administrator.</div>";
    exit;
}

$sql = "SELECT * FROM applications WHERE assigned_mentor = $mentor_id ORDER BY submitted_at DESC";

$mentees = [];
$res = $conn->query($sql);
if ($res) while ($row = $res->fetch_assoc()) $mentees[] = $row;
$conn->close();
$page_title = 'My Mentees';
$breadcrumb_items = ['Mentees'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">My Mentees</h2>
    <?php if (empty($mentees)): ?>
        <div class="alert alert-info">No mentees assigned to you yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Business Idea</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($mentees as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['full_name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['program']) ?></td>
                    <td><?= htmlspecialchars($m['business_idea_name']) ?></td>
                    <td><?= htmlspecialchars($m['status']) ?></td>
                    <td><?= htmlspecialchars($m['submitted_at']) ?></td>
                    <td class="d-flex flex-column gap-2">
                        <a href="application_view_mentor.php?id=<?= $m['id'] ?>" class="btn btn-info btn-sm mb-1">View</a>
                        <form method="post" action="../send_message.php" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="application_id" value="<?= $m['id'] ?>">
                            <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($m['email']) ?>">
                            <input type="text" name="message" class="form-control form-control-sm" placeholder="Type message..." required style="max-width: 180px;">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php
$page_content = ob_get_clean();
include 'template_mentor.php'; 