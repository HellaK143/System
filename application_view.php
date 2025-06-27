<?php
session_start();
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || intval($_GET['id']) <= 0) {
    header('Location: index.php');
    exit;
}
$app_id = intval($_GET['id']);
if (!empty($_SESSION['msg_success'])) {
    echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['msg_success']).'</div>';
    unset($_SESSION['msg_success']);
}
if (!empty($_SESSION['msg_error'])) {
    echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['msg_error']).'</div>';
    unset($_SESSION['msg_error']);
} 