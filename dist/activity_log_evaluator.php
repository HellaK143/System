<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'evaluator') die('Access denied.');
$page_title = 'Evaluator Activity Log';
$breadcrumb_items = ['Activity Log'];
ob_start();
?>
<div class="container my-5">
    <h2>Recent Activities</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Date</th><th>Activity</th></tr></thead>
        <tbody>
            <tr><td>2024-07-01</td><td>Logged in</td></tr>
            <tr><td>2024-07-01</td><td>Evaluated an application</td></tr>
            <tr><td>2024-07-01</td><td>Updated profile</td></tr>
        </tbody>
    </table>
</div>
<?php
$page_content = ob_get_clean();
include 'template_evaluator.php'; 