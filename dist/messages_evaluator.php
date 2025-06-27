<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'evaluator') die('Access denied.');
$page_title = 'Evaluator Messages';
$breadcrumb_items = ['Messages'];
ob_start();
?>
<div class="container my-5">
    <h2>Messages</h2>
    <div class="alert alert-info">Messaging functionality coming soon.</div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 