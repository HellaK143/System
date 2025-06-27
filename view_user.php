<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$id = intval($_GET['id'] ?? 0);
if (!$id) die('Invalid user ID');

// Get user details
$user = $conn->query("SELECT * FROM users WHERE user_id=$id")->fetch_assoc();
if (!$user) die('User not found');

// Helper function to safely get count
function safeCount($conn, $table, $where = '') {
    $sql = "SELECT COUNT(*) as count FROM $table";
    if ($where) $sql .= " WHERE $where";
    $result = $conn->query($sql);
    if ($result === false) return 0; // Table doesn't exist or query failed
    return $result->fetch_assoc()['count'];
}

// Helper function to safely get query result
function safeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if ($result === false) return false; // Query failed
    return $result;
}

// Get user statistics based on role
$stats = [];
if ($user['role'] === 'entrepreneur') {
    $stats['applications'] = safeCount($conn, 'applications', "user_id=$id");
    $stats['bookings'] = safeCount($conn, 'bookings', "user_id=$id");
    $stats['messages'] = safeCount($conn, 'messages', "sender_id=$id OR receiver_id=$id");
} elseif ($user['role'] === 'mentor') {
    $stats['sessions'] = safeCount($conn, 'sessions', "mentor_id=$id");
    $stats['bookings'] = safeCount($conn, 'bookings', "mentor_id=$id");
    $stats['messages'] = safeCount($conn, 'messages', "sender_id=$id OR receiver_id=$id");
} elseif ($user['role'] === 'evaluator') {
    $stats['evaluations'] = safeCount($conn, 'applications', "evaluator_id=$id");
    $stats['messages'] = safeCount($conn, 'messages', "sender_id=$id OR receiver_id=$id");
} else {
    $stats['total_users'] = safeCount($conn, 'users');
    $stats['total_applications'] = safeCount($conn, 'applications');
    $stats['total_bookings'] = safeCount($conn, 'bookings');
}

// Get recent messages
$recent_messages = safeQuery($conn, "
    SELECT m.*, u.username as sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.user_id 
    WHERE m.recipient_id = $id OR m.sender_id = $id 
    ORDER BY m.sent_at DESC 
    LIMIT 5
");

// Get recent activity based on role
$recent_activity = false;
if ($user['role'] === 'entrepreneur') {
    $recent_activity = safeQuery($conn, "
        SELECT 'application' as type, application_id as id, title, created_at as date
        FROM applications 
        WHERE user_id = $id 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
} elseif ($user['role'] === 'mentor') {
    $recent_activity = safeQuery($conn, "
        SELECT 'session' as type, session_id as id, title, created_at as date
        FROM sessions 
        WHERE mentor_id = $id 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
}

$page_title = 'User Dashboard - ' . $user['username'];
$breadcrumb_items = ['Manage Users', $user['username']];

ob_start();
?>

<!-- User Profile Card -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="user-avatar mb-3">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="mb-2"><?= htmlspecialchars($user['username']) ?></h4>
                <span class="badge bg-primary fs-6 mb-3"><?= htmlspecialchars(ucfirst($user['role'])) ?></span>
                <hr>
                <div class="text-start">
                    <p class="mb-2"><strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong><br><?= htmlspecialchars($user['email']) ?></p>
                    <p class="mb-0"><strong><i class="fas fa-id-badge me-2 text-primary"></i>User ID:</strong><br><?= htmlspecialchars($user['user_id']) ?></p>
                </div>
                <hr>
                <div class="d-flex justify-content-center gap-2">
                    <a href="edit_user.php?id=<?= $id ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="message_user.php?id=<?= $id ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-envelope me-1"></i>Message
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-md-8">
        <div class="row">
            <?php if ($user['role'] === 'entrepreneur'): ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-primary mb-2"><i class="fas fa-file-alt fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['applications'] ?></div>
                            <div class="stat-label text-muted">Applications</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-success mb-2"><i class="fas fa-calendar-check fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['bookings'] ?></div>
                            <div class="stat-label text-muted">Bookings</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-info mb-2"><i class="fas fa-comments fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['messages'] ?></div>
                            <div class="stat-label text-muted">Messages</div>
                        </div>
                    </div>
                </div>
            <?php elseif ($user['role'] === 'mentor'): ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-warning mb-2"><i class="fas fa-chalkboard-teacher fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['sessions'] ?></div>
                            <div class="stat-label text-muted">Sessions</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-success mb-2"><i class="fas fa-calendar-check fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['bookings'] ?></div>
                            <div class="stat-label text-muted">Bookings</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-info mb-2"><i class="fas fa-comments fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['messages'] ?></div>
                            <div class="stat-label text-muted">Messages</div>
                        </div>
                    </div>
                </div>
            <?php elseif ($user['role'] === 'evaluator'): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-danger mb-2"><i class="fas fa-clipboard-check fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['evaluations'] ?></div>
                            <div class="stat-label text-muted">Evaluations</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-info mb-2"><i class="fas fa-comments fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['messages'] ?></div>
                            <div class="stat-label text-muted">Messages</div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-primary mb-2"><i class="fas fa-users fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['total_users'] ?></div>
                            <div class="stat-label text-muted">Total Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-success mb-2"><i class="fas fa-file-alt fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['total_applications'] ?></div>
                            <div class="stat-label text-muted">Applications</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon text-warning mb-2"><i class="fas fa-calendar-check fa-2x"></i></div>
                            <div class="stat-value text-dark fw-bold"><?= $stats['total_bookings'] ?></div>
                            <div class="stat-label text-muted">Bookings</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Activity and Messages -->
<div class="row">
    <!-- Recent Activity -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_activity && $recent_activity->num_rows > 0): ?>
                    <?php while($activity = $recent_activity->fetch_assoc()): ?>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-<?= $activity['type'] === 'application' ? 'file-alt' : 'chalkboard-teacher' ?> me-2 text-primary"></i>
                                    <strong><?= htmlspecialchars($activity['title']) ?></strong>
                                </div>
                                <small class="text-muted"><?= date('M j, Y', strtotime($activity['date'])) ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center mb-0">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Recent Messages</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_messages && $recent_messages->num_rows > 0): ?>
                    <?php while($message = $recent_messages->fetch_assoc()): ?>
                        <div class="message-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="text-primary"><?= htmlspecialchars($message['sender_name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($message['subject']) ?></small>
                                </div>
                                <small class="text-muted"><?= date('M j, Y', strtotime($message['sent_at'])) ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center mb-0">No recent messages</p>
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
.stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}
.activity-item {
    padding: 12px 15px;
    border-left: 4px solid #667eea;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
    transition: all 0.3s ease;
}
.activity-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}
.message-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}
.message-item:hover {
    background-color: #f8f9fa;
}
.message-item:last-child {
    border-bottom: none;
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
</style>
";

include 'dist/template_admin.php';
?> 