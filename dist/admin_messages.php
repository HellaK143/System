<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
$admin_email = $_SESSION['email'];
// Fetch messages
$unread = $conn->query("SELECT * FROM messages WHERE recipient = '$admin_email' AND is_read = 0 ORDER BY sent_at DESC");
$read = $conn->query("SELECT * FROM messages WHERE recipient = '$admin_email' AND is_read = 1 ORDER BY sent_at DESC");
$sent = $conn->query("SELECT * FROM messages WHERE sender = '$admin_email' ORDER BY sent_at DESC");
// Fetch all users, mentors, entrepreneurs, evaluators for messaging
$all_users = $conn->query("SELECT email, username, role FROM users");
$all_mentors = $conn->query("SELECT email, full_name FROM mentors");
$all_entrepreneurs = $conn->query("SELECT email, first_name, last_name FROM entrepreneur");
$all_evaluators = $conn->query("SELECT email, username FROM users WHERE role='evaluator'");
$conn->close();
$page_title = 'Admin Messages';
$breadcrumb_items = ['Messages'];
$additional_css = '.messages-sidebar { position: sticky; top: 90px; background: #f8f9fa; border-radius: 1rem; min-height: 400px; padding: 2rem 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); } .messages-sidebar .nav-link { color: #333; font-weight: 500; border-radius: 0.5rem; margin-bottom: 0.5rem; } .messages-sidebar .nav-link.active, .messages-sidebar .nav-link:focus { background: #007bff; color: #fff; } .messages-content { min-height: 400px; } .message-card { border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 1.2rem; background: #fff; } .message-card .card-header { border-radius: 1rem 1rem 0 0; } .message-card .card-body { padding: 1.25rem; } .floating-compose { position: fixed; bottom: 2rem; right: 2rem; z-index: 1050; } @media (max-width: 991px) { .messages-sidebar { display: none; } .floating-compose { display: block; } }';
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Messages</h2>
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="messages-sidebar">
                <nav class="nav flex-column nav-pills">
                    <a class="nav-link active" id="unread-tab-link" data-bs-toggle="tab" href="#unread" role="tab"><i class="fas fa-envelope-open-text me-2"></i>Unread</a>
                    <a class="nav-link" id="read-tab-link" data-bs-toggle="tab" href="#read" role="tab"><i class="fas fa-envelope me-2"></i>Read</a>
                    <a class="nav-link" id="sent-tab-link" data-bs-toggle="tab" href="#sent" role="tab"><i class="fas fa-paper-plane me-2"></i>Sent</a>
                    <a class="nav-link" id="compose-tab-link" data-bs-toggle="tab" href="#compose" role="tab"><i class="fas fa-envelope me-2"></i>Compose</a>
                </nav>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="tab-content messages-content" id="messagesTabContent">
                <div class="tab-pane fade show active" id="unread" role="tabpanel">
                    <div class="message-card card mb-4">
                        <div class="card-header bg-primary text-white"><i class="fas fa-envelope-open-text me-2"></i>Unread Messages</div>
                        <div class="card-body p-0">
                            <?php if ($unread && $unread->num_rows > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php while ($msg = $unread->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <div class="fw-bold">From: <?= htmlspecialchars($msg['sender']) ?></div>
                                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                        <div class="small text-muted">Sent: <?= htmlspecialchars($msg['sent_at']) ?></div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <?php else: ?><div class="p-3 text-muted">No unread messages.</div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="read" role="tabpanel">
                    <div class="message-card card mb-4">
                        <div class="card-header bg-info text-white"><i class="fas fa-envelope me-2"></i>Read Messages</div>
                        <div class="card-body p-0">
                            <?php if ($read && $read->num_rows > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php while ($msg = $read->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <div class="fw-bold">From: <?= htmlspecialchars($msg['sender']) ?></div>
                                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                        <div class="small text-muted">Sent: <?= htmlspecialchars($msg['sent_at']) ?></div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <?php else: ?><div class="p-3 text-muted">No read messages.</div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="sent" role="tabpanel">
                    <div class="message-card card mb-4">
                        <div class="card-header bg-success text-white"><i class="fas fa-paper-plane me-2"></i>Sent Messages</div>
                        <div class="card-body p-0">
                            <?php if ($sent && $sent->num_rows > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php while ($msg = $sent->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <div class="fw-bold">To: <?= htmlspecialchars($msg['recipient']) ?></div>
                                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                        <div class="small text-muted">Sent: <?= htmlspecialchars($msg['sent_at']) ?></div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <?php else: ?><div class="p-3 text-muted">No sent messages.</div><?php endif; ?>
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
                                    <select name="recipient_email" id="recipient_email" class="form-select" required>
                                        <option value="">-- Select Recipient --</option>
                                        <optgroup label="Users">
                                        <?php if ($all_users) while($u = $all_users->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($u['email']) ?>">User: <?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['role']) ?>)</option>
                                        <?php endwhile; ?>
                                        </optgroup>
                                        <optgroup label="Mentors">
                                        <?php if ($all_mentors) while($m = $all_mentors->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($m['email']) ?>">Mentor: <?= htmlspecialchars($m['full_name']) ?></option>
                                        <?php endwhile; ?>
                                        </optgroup>
                                        <optgroup label="Entrepreneurs">
                                        <?php if ($all_entrepreneurs) while($e = $all_entrepreneurs->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($e['email']) ?>">Entrepreneur: <?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                                        <?php endwhile; ?>
                                        </optgroup>
                                        <optgroup label="Evaluators">
                                        <?php if ($all_evaluators) while($ev = $all_evaluators->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($ev['email']) ?>">Evaluator: <?= htmlspecialchars($ev['username']) ?></option>
                                        <?php endwhile; ?>
                                        </optgroup>
                                    </select>
                                </div>
                                <input type="hidden" name="application_id" value="0">
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
<?php
$page_content = ob_get_clean();
include __DIR__ . '/template_admin.php'; 