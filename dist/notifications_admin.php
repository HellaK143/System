<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$page_title = 'Admin Notifications';
$breadcrumb_items = ['Notifications'];
$additional_css = '.notification-card { border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 1.2rem; } .notification-icon { font-size: 1.5rem; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 1rem; } .notification-info { background: #e3f2fd; color: #1976d2; } .notification-warning { background: #fff3cd; color: #856404; } .notification-success { background: #e6f4ea; color: #218838; }';
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Notifications</h2>
    <div class="notification-card d-flex align-items-center p-3 notification-info">
        <div class="notification-icon bg-white"><i class="fas fa-user-plus"></i></div>
        <div>
            <div class="fw-bold">New user registered</div>
            <div class="small text-muted">A new user has registered. Review their details in the dashboard.</div>
        </div>
    </div>
    <div class="notification-card d-flex align-items-center p-3 notification-warning">
        <div class="notification-icon bg-white"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="fw-bold">System alert</div>
            <div class="small text-muted">There is a pending system update. Please review the system status.</div>
        </div>
    </div>
    <div class="notification-card d-flex align-items-center p-3 notification-success">
        <div class="notification-icon bg-white"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="fw-bold">Backup completed</div>
            <div class="small text-muted">The system backup was completed successfully.</div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/template_admin.php'; 