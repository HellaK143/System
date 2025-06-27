<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) die('Invalid user ID');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$user = $conn->query("SELECT username, email, role FROM users WHERE user_id=$id")->fetch_assoc();
if (!$user) die('User not found');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (!$message) {
        $error = 'Message is required.';
    } else {
        $message = $conn->real_escape_string($message);
        $sender_id = intval($_SESSION['user_id']);
        $sql = "INSERT INTO messages (sender_id, recipient_id, message, sent_at) VALUES ($sender_id, $id, '$message', NOW())";
        if ($conn->query($sql)) {
            header('Location: users_admin.php?success=Message+sent'); exit;
        } else {
            $error = 'DB error: ' . $conn->error;
        }
    }
}

// Get recent messages between admin and this user
$recent_messages = $conn->query("
    SELECT m.*, u.username as sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.user_id 
    WHERE (m.recipient_id = $id AND m.sender_id = " . $_SESSION['user_id'] . ") 
       OR (m.sender_id = $id AND m.recipient_id = " . $_SESSION['user_id'] . ")
    ORDER BY m.sent_at DESC 
    LIMIT 5
");

$page_title = 'Message User - ' . $user['username'];
$breadcrumb_items = ['Manage Users', $user['username'], 'Message'];

ob_start();
?>

<div class="row">
    <!-- Message Form -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Send Message</h5>
            </div>
            <div class="card-body p-4">
                <!-- User Profile Header -->
                <div class="text-center mb-4">
                    <div class="user-avatar mb-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="mb-2"><?= htmlspecialchars($user['username']) ?></h4>
                    <span class="badge bg-primary fs-6 mb-3"><?= htmlspecialchars(ucfirst($user['role'])) ?></span>
                    <p class="text-muted mb-0"><?= htmlspecialchars($user['email']) ?></p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-comment me-2 text-primary"></i>Message
                        </label>
                        <textarea name="message" class="form-control" rows="6" 
                                  placeholder="Type your message here..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="users_admin.php" class="btn btn-secondary btn-custom">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-custom">
                            <i class="fas fa-paper-plane me-1"></i>Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Messages</h5>
            </div>
            <div class="card-body p-4">
                <?php if ($recent_messages && $recent_messages->num_rows > 0): ?>
                    <?php while($msg = $recent_messages->fetch_assoc()): ?>
                        <div class="message-item <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'message-sent' : 'message-received' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="text-primary"><?= htmlspecialchars($msg['sender_name']) ?></strong>
                                    <span class="badge <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'bg-primary' : 'bg-secondary' ?> ms-2">
                                        <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'You' : 'Them' ?>
                                    </span>
                                </div>
                                <span class="message-time"><?= date('M j, Y g:i A', strtotime($msg['sent_at'])) ?></span>
                            </div>
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">No previous messages with this user</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// Additional CSS for custom styling
$additional_css = "
<style>
.user-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto;
}
.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
.btn-custom {
    border-radius: 10px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.message-item {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}
.message-sent {
    background: #e3f2fd;
    border-left-color: #2196f3;
}
.message-received {
    background: #f3e5f5;
    border-left-color: #9c27b0;
}
.message-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.message-time {
    font-size: 0.8rem;
    color: #666;
}
.message-content {
    background: rgba(255,255,255,0.7);
    padding: 10px;
    border-radius: 8px;
    margin-top: 8px;
}
.card {
    transition: all 0.3s ease;
    border-radius: 15px;
}
.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
</style>
";

include 'dist/template_admin.php';
?> 