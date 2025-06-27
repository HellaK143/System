<?php
session_start();
require_once 'db.php';
secure_session_check();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Handle connect action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connect'])) {
    $mentor_id = intval($_POST['mentor_id']);
    $entrepreneur_id = intval($_POST['entrepreneur_id']);
    // Insert connection
    $conn->query("CREATE TABLE IF NOT EXISTS mentor_entrepreneur (id INT AUTO_INCREMENT PRIMARY KEY, mentor_id INT, entrepreneur_id INT, assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $stmt = $conn->prepare("INSERT INTO mentor_entrepreneur (mentor_id, entrepreneur_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $mentor_id, $entrepreneur_id);
    $stmt->execute();
    $stmt->close();
    // Fetch mentor and entrepreneur info
    $mentor = $conn->query("SELECT * FROM mentors WHERE mentor_id=$mentor_id")->fetch_assoc();
    $entrepreneur = $conn->query("SELECT * FROM entrepreneur WHERE entrepreneur_id=$entrepreneur_id")->fetch_assoc();
    // Notify mentor
    $mentor_user_id = get_user_id_by_email($mentor['email']);
    if ($mentor_user_id) {
        add_notification($mentor_user_id, 'info', 'mentor', 'New Entrepreneur Assigned', 'You have been assigned to entrepreneur: ' . $entrepreneur['first_name'] . ' ' . $entrepreneur['last_name'] . ' (' . $entrepreneur['email'] . ')');
    }
    // Notify entrepreneur
    $entrepreneur_user_id = get_user_id_by_email($entrepreneur['email']);
    if ($entrepreneur_user_id) {
        add_notification($entrepreneur_user_id, 'info', 'mentor', 'Mentor Assigned', 'A mentor (' . $mentor['full_name'] . ') has been assigned to you.');
    }
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'mentor', 'Mentor-Entrepreneur Connection', 'Mentor ' . $mentor['full_name'] . ' connected to entrepreneur ' . $entrepreneur['first_name'] . ' ' . $entrepreneur['last_name']);
    }
    header('Location: connect_mentor_entrepreneur.php?success=1'); exit;
}
// Fetch all entrepreneurs and mentors
$entrepreneurs = $conn->query("SELECT entrepreneur_id, first_name, last_name, email FROM entrepreneur ORDER BY first_name, last_name");
$mentors = $conn->query("SELECT mentor_id, full_name, email FROM mentors ORDER BY full_name");
$conn->close();
$page_title = 'Connect Mentor to Entrepreneur';
$breadcrumb_items = ['Mentors', 'Connect Mentor to Entrepreneur'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Connect Mentor to Entrepreneur</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Mentor successfully connected to entrepreneur and notifications sent.</div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Entrepreneur</th>
                    <th>Email</th>
                    <th>Mentor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entrepreneurs && $entrepreneurs->num_rows > 0): while($e = $entrepreneurs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td>
                        <form method="post" class="d-flex align-items-center gap-2 mb-0">
                            <input type="hidden" name="entrepreneur_id" value="<?= $e['entrepreneur_id'] ?>">
                            <select name="mentor_id" class="form-select form-select-sm" required style="width:auto;display:inline-block">
                                <option value="">Select Mentor</option>
                                <?php if ($mentors): mysqli_data_seek($mentors, 0); while($m = $mentors->fetch_assoc()): ?>
                                    <option value="<?= $m['mentor_id'] ?>"><?= htmlspecialchars($m['full_name']) ?> (<?= htmlspecialchars($m['email']) ?>)</option>
                                <?php endwhile; endif; ?>
                            </select>
                    </td>
                    <td>
                            <button type="submit" name="connect" class="btn btn-primary btn-sm">Connect</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="4" class="text-center text-muted">No entrepreneurs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 