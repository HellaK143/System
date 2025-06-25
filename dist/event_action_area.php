<?php
session_start();
require_once '../db.php';
$id = intval($_GET['id'] ?? 0);
$conn = new mysqli($host, $user, $password, $dbname);
$user_id = $_SESSION['user_id'] ?? 0;
// Check invitation/registration status
$invited = $conn->query("SELECT status FROM event_invitations WHERE event_id = $id AND user_id = $user_id")->fetch_assoc()['status'] ?? null;
$registered = $conn->query("SELECT id FROM event_registrations WHERE event_id = $id AND user_id = $user_id")->num_rows > 0;
if ($invited && $invited === 'pending') {
    echo '<button class="btn btn-success me-2" onclick="eventAction(\'accept\')">Accept Invitation</button>';
    echo '<button class="btn btn-danger" onclick="eventAction(\'decline\')">Decline Invitation</button>';
} elseif (!$registered) {
    echo '<button class="btn btn-primary" onclick="eventAction(\'register\')">Register for Event</button>';
}
?>
<script>
function eventAction(action) {
  fetch('event_action_handler.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({event_id: <?= $id ?>, action: action})
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('eventAlertArea').innerHTML = '<div class="alert alert-'+(data.success?'success':'danger')+'">'+data.message+'</div>';
    setTimeout(()=>location.reload(), 1200);
  });
}
</script> 