<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') die('Access denied.');
require_once '../db.php';
$user_id = $_SESSION['user_id'];
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
// Get mentor email from users table
$user_res = $conn->query("SELECT email FROM users WHERE user_id = $user_id");
$mentor_email_users = '';
if ($user_res && $u = $user_res->fetch_assoc()) $mentor_email_users = strtolower(trim($u['email']));
// Get mentor email from mentors table
$mentor_email_mentors = '';
$mentor_res = $conn->query("SELECT email FROM mentors WHERE user_id = $user_id OR LOWER(TRIM(email)) = '" . $conn->real_escape_string($mentor_email_users) . "'");
if ($mentor_res && $m = $mentor_res->fetch_assoc()) $mentor_email_mentors = strtolower(trim($m['email']));
// Build list of all possible mentor emails
$mentor_emails = array_unique(array_filter([$mentor_email_users, $mentor_email_mentors]));
$mentor_emails_sql = "'" . implode("','", array_map([$conn, 'real_escape_string'], $mentor_emails)) . "'";
// Get admin emails and names
$admins = [];
$admin_res = $conn->query("SELECT username, email FROM users WHERE role = 'admin'");
if ($admin_res) while ($a = $admin_res->fetch_assoc()) $admins[] = $a;
// Get assigned applicants' names, emails, and application IDs
$mentees = [];
$where_mentees = [];
foreach ($mentor_emails as $em) {
    $where_mentees[] = "LOWER(TRIM(assigned_mentor_email)) = '" . $conn->real_escape_string($em) . "'";
}
$where_mentees[] = "assigned_mentor = $user_id";
$where_mentees_sql = $where_mentees ? ('WHERE ' . implode(' OR ', $where_mentees)) : '';
$app_res = $conn->query("SELECT DISTINCT id, full_name, email FROM applications $where_mentees_sql");
if ($app_res) while ($a = $app_res->fetch_assoc()) $mentees[] = $a;
// Messages from admin (application_id = 0)
$admin_emails = array_map(function($a) use ($conn) { return strtolower(trim($a['email'])); }, $admins);
$from_admin = [];
if ($admin_emails && $mentor_emails) {
    $admin_sql = "'" . implode("','", array_map([$conn, 'real_escape_string'], $admin_emails)) . "'";
    $res = $conn->query("SELECT * FROM messages WHERE application_id = 0 AND LOWER(TRIM(recipient)) IN ($mentor_emails_sql) AND LOWER(TRIM(sender)) IN ($admin_sql) ORDER BY sent_at DESC");
    if ($res) while ($m = $res->fetch_assoc()) $from_admin[] = $m;
}
// Messages from assigned applicants (application_id matches mentee)
$applicant_emails = array_map(function($m) use ($conn) { return strtolower(trim($m['email'])); }, $mentees);
$applicant_ids = array_map(function($m) { return intval($m['id']); }, $mentees);
$from_applicants = [];
if ($applicant_emails && $mentor_emails && $applicant_ids) {
    $app_sql = "'" . implode("','", array_map([$conn, 'real_escape_string'], $applicant_emails)) . "'";
    $app_ids_sql = implode(",", $applicant_ids);
    $res = $conn->query("SELECT * FROM messages WHERE application_id IN ($app_ids_sql) AND LOWER(TRIM(recipient)) IN ($mentor_emails_sql) AND LOWER(TRIM(sender)) IN ($app_sql) ORDER BY sent_at DESC");
    if ($res) while ($m = $res->fetch_assoc()) $from_applicants[] = $m;
}
// All sent messages (by mentor, any application_id)
$sent = [];
$res = $conn->query("SELECT * FROM messages WHERE LOWER(TRIM(sender)) IN ($mentor_emails_sql) ORDER BY sent_at DESC");
if ($res) while ($m = $res->fetch_assoc()) $sent[] = $m;
$conn->close();
$page_title = 'Mentor Messages';
$breadcrumb_items = ['Messages'];
$additional_css = '.messages-sidebar { position: sticky; top: 90px; background: #f8f9fa; border-radius: 1rem; min-height: 400px; padding: 2rem 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); } .messages-sidebar .nav-link { color: #333; font-weight: 500; border-radius: 0.5rem; margin-bottom: 0.5rem; } .messages-sidebar .nav-link.active, .messages-sidebar .nav-link:focus { background: #007bff; color: #fff; } .messages-content { min-height: 400px; } .message-card { border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 1.2rem; background: #fff; } .message-card .card-header { border-radius: 1rem 1rem 0 0; } .message-card .card-body { padding: 1.25rem; } .floating-compose { position: fixed; bottom: 2rem; right: 2rem; z-index: 1050; } @media (max-width: 991px) { .messages-sidebar { display: none; } .floating-compose { display: block; } }';
ob_start();
?>
<div class="container my-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="messages-sidebar">
                <nav class="nav flex-column nav-pills">
                    <a class="nav-link active" id="admin-tab-link" data-bs-toggle="tab" href="#admin" role="tab"><i class="fas fa-user-shield me-2"></i>From Admin</a>
                    <a class="nav-link" id="applicants-tab-link" data-bs-toggle="tab" href="#applicants" role="tab"><i class="fas fa-user-graduate me-2"></i>From Applicants</a>
                    <a class="nav-link" id="sent-tab-link" data-bs-toggle="tab" href="#sent" role="tab"><i class="fas fa-paper-plane me-2"></i>Sent</a>
                    <a class="nav-link" id="compose-tab-link" data-bs-toggle="tab" href="#compose" role="tab"><i class="fas fa-envelope me-2"></i>Compose</a>
                </nav>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="tab-content messages-content" id="messagesTabContent">
                <div class="tab-pane fade show active" id="admin" role="tabpanel">
                    <div class="message-card card mb-4">
                        <div class="card-header bg-primary text-white"><i class="fas fa-user-shield me-2"></i>Messages from Admin</div>
                        <div class="card-body p-0">
                            <?php if (empty($from_admin)): ?><div class="p-3 text-muted">No messages from admin.</div><?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($from_admin as $msg): ?>
                                    <li class="list-group-item">
                                        <div class="fw-bold">From: <?= htmlspecialchars($msg['sender']) ?></div>
                                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                        <div class="small text-muted">Sent: <?= htmlspecialchars($msg['sent_at']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="applicants" role="tabpanel">
                    <div class="message-card card mb-4">
                        <div class="card-header bg-info text-white"><i class="fas fa-user-graduate me-2"></i>Messages from Assigned Applicants</div>
                        <div class="card-body p-0">
                            <?php if (empty($from_applicants)): ?><div class="p-3 text-muted">No messages from applicants.</div><?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($from_applicants as $msg): ?>
                                    <li class="list-group-item">
                                        <div class="fw-bold">From: <?= htmlspecialchars($msg['sender']) ?></div>
                                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                        <div class="small text-muted">Sent: <?= htmlspecialchars($msg['sent_at']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="sent" role="tabpanel">
                    <div class="message-card card mb-4">
                        <div class="card-header bg-success text-white"><i class="fas fa-paper-plane me-2"></i>Sent Messages</div>
                        <div class="card-body p-0">
                            <?php if (empty($sent)): ?><div class="p-3 text-muted">No sent messages.</div><?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($sent as $msg): ?>
                                    <li class="list-group-item">
                                        <div class="fw-bold">To: <?= htmlspecialchars($msg['recipient']) ?></div>
                                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                        <div class="small text-muted">Sent: <?= htmlspecialchars($msg['sent_at']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="compose" role="tabpanel">
                    <div class="message-card card mt-4">
                        <div class="card-header bg-light"><i class="fas fa-envelope text-primary"></i> Send Message</div>
                        <div class="card-body">
                            <form method="post" action="../send_message.php" id="composeForm">
                                <div class="mb-3">
                                    <label for="recipient_email" class="form-label">Select Recipient</label>
                                    <select name="recipient_email" id="recipient_email" class="form-select" required onchange="updateApplicationId()">
                                        <option value="">-- Select Recipient --</option>
                                        <optgroup label="Admins">
                                        <?php foreach ($admins as $a): ?>
                                            <option value="<?= htmlspecialchars($a['email']) ?>" data-application-id="0">Admin: <?= htmlspecialchars($a['username']) ?> (<?= htmlspecialchars($a['email']) ?>)</option>
                                        <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Assigned Applicants">
                                        <?php foreach ($mentees as $m): ?>
                                            <option value="<?= htmlspecialchars($m['email']) ?>" data-application-id="<?= htmlspecialchars($m['id']) ?>"><?= htmlspecialchars($m['full_name']) ?> (<?= htmlspecialchars($m['email']) ?>)</option>
                                        <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                                <input type="hidden" name="application_id" id="application_id" value="0">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Floating Compose Button for Mobile -->
    <a href="#compose" class="btn btn-primary btn-lg rounded-circle floating-compose d-lg-none" data-bs-toggle="tab" role="tab" aria-controls="compose" aria-selected="false" title="Compose Message">
        <i class="fas fa-plus"></i>
    </a>
</div>
<script>
function updateApplicationId() {
    var select = document.getElementById('recipient_email');
    var appId = select.options[select.selectedIndex].getAttribute('data-application-id') || '0';
    document.getElementById('application_id').value = appId;
}
</script>
<?php
$page_content = ob_get_clean();
include 'template_mentor.php'; 