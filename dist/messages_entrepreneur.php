<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entrepreneur') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$email = $_SESSION['email'];
// Fetch all admins
$admins = $conn->query("SELECT user_id, username, email FROM users WHERE role='admin' ORDER BY username");
// Fetch all mentors
$mentors = $conn->query("SELECT mentor_id, full_name, email FROM mentors ORDER BY full_name");
// Fetch all evaluators
$evaluators = $conn->query("SELECT user_id, username, email FROM users WHERE role='evaluator' ORDER BY username");
// Fetch all entrepreneurs
$entrepreneurs = $conn->query("SELECT entrepreneur_id, first_name, last_name, email FROM entrepreneur ORDER BY first_name, last_name");
$success = $error = '';
// Handle send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $recipient_role = $_POST['recipient_role'];
    $recipient_email = '';
    if ($recipient_role === 'admin') $recipient_email = $_POST['recipient_email_admin'];
    if ($recipient_role === 'mentor') $recipient_email = $_POST['recipient_email_mentor'];
    if ($recipient_role === 'evaluator') $recipient_email = $_POST['recipient_email_evaluator'];
    if ($recipient_role === 'entrepreneur') $recipient_email = $_POST['recipient_email_entrepreneur'];
    $message = trim($_POST['message']);
    if ($recipient_role && $recipient_email && $message) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_email, recipient_email, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('sss', $email, $recipient_email, $message);
        if ($stmt->execute()) {
            $success = 'Message sent!';
        } else {
            $error = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = 'All fields are required.';
    }
}
// Inbox
$inbox = $conn->query("SELECT * FROM messages WHERE recipient_email='$email' ORDER BY sent_at DESC");
// Sent
$sent = $conn->query("SELECT * FROM messages WHERE sender_email='$email' ORDER BY sent_at DESC");
$conn->close();
$page_title = 'Messages';
$breadcrumb_items = ['Messages'];
ob_start();
?>
<div class="container my-4">
    <h3 class="mb-4">Messages</h3>
    <?php if ($success): ?><div class="alert alert-success"> <?= htmlspecialchars($success) ?> </div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div><?php endif; ?>
    <ul class="nav nav-tabs mb-3" id="msgTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox" type="button" role="tab">Inbox</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">Sent</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="compose-tab" data-bs-toggle="tab" data-bs-target="#compose" type="button" role="tab">Compose</button>
        </li>
    </ul>
    <div class="tab-content" id="msgTabContent">
        <div class="tab-pane fade show active" id="inbox" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light"><tr><th>From</th><th>Message</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php if ($inbox && $inbox->num_rows > 0): while($m = $inbox->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['sender_email']) ?></td>
                            <td><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                            <td><?= htmlspecialchars($m['sent_at']) ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="3" class="text-center text-muted">No messages.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="sent" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light"><tr><th>To</th><th>Message</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php if ($sent && $sent->num_rows > 0): while($m = $sent->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['recipient_email']) ?></td>
                            <td><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                            <td><?= htmlspecialchars($m['sent_at']) ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="3" class="text-center text-muted">No messages.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="compose" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="send_message" value="1">
                <div class="mb-3">
                    <label class="form-label">Recipient Role</label>
                    <select name="recipient_role" id="recipient_role" class="form-select" required onchange="showRecipientDropdown()">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="mentor">Mentor</option>
                        <option value="evaluator">Evaluator</option>
                        <option value="entrepreneur">Entrepreneur</option>
                    </select>
                </div>
                <div class="mb-3" id="recipient_admin" style="display:none;">
                    <label class="form-label">Select Admin</label>
                    <select name="recipient_email_admin" class="form-select">
                        <option value="">Select Admin</option>
                        <?php if ($admins) while($a = $admins->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($a['email']) ?>">Admin: <?= htmlspecialchars($a['username']) ?> (<?= htmlspecialchars($a['email']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3" id="recipient_mentor" style="display:none;">
                    <label class="form-label">Select Mentor</label>
                    <select name="recipient_email_mentor" class="form-select">
                        <option value="">Select Mentor</option>
                        <?php if ($mentors) while($m = $mentors->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($m['email']) ?>">Mentor: <?= htmlspecialchars($m['full_name']) ?> (<?= htmlspecialchars($m['email']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3" id="recipient_evaluator" style="display:none;">
                    <label class="form-label">Select Evaluator</label>
                    <select name="recipient_email_evaluator" class="form-select">
                        <option value="">Select Evaluator</option>
                        <?php if ($evaluators) while($e = $evaluators->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($e['email']) ?>">Evaluator: <?= htmlspecialchars($e['username']) ?> (<?= htmlspecialchars($e['email']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3" id="recipient_entrepreneur" style="display:none;">
                    <label class="form-label">Select Entrepreneur</label>
                    <select name="recipient_email_entrepreneur" class="form-select">
                        <option value="">Select Entrepreneur</option>
                        <?php if ($entrepreneurs) while($en = $entrepreneurs->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($en['email']) ?>">Entrepreneur: <?= htmlspecialchars($en['first_name'] . ' ' . $en['last_name']) ?> (<?= htmlspecialchars($en['email']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
            <script>
            function showRecipientDropdown() {
                var role = document.getElementById('recipient_role').value;
                document.getElementById('recipient_admin').style.display = (role === 'admin') ? '' : 'none';
                document.getElementById('recipient_mentor').style.display = (role === 'mentor') ? '' : 'none';
                document.getElementById('recipient_evaluator').style.display = (role === 'evaluator') ? '' : 'none';
                document.getElementById('recipient_entrepreneur').style.display = (role === 'entrepreneur') ? '' : 'none';
            }
            </script>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_entrepreneur.php'; 