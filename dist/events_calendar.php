<?php
session_start();
require_once '../db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$res = $conn->query("SELECT * FROM events ORDER BY start_datetime");
$events = [];
while ($e = $res->fetch_assoc()) {
    $month = date('F Y', strtotime($e['start_datetime']));
    $events[$month][] = $e;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar integration -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
</head>
<body>
<div class="container mt-5">
    <a href="../dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="add_event.php" class="btn btn-primary mb-3 ms-2">Add Event</a>
    <?php endif; ?>
    <h2>Event Calendar</h2>
    <!-- Filter/search UI -->
    <form id="eventFilterForm" class="row g-2 mb-3">
      <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="Search title/location"></div>
      <div class="col-md-2">
        <select name="type" class="form-select">
          <option value="">All Types</option>
          <option value="workshop">Workshop</option>
          <option value="training">Training</option>
          <option value="mentoring">Mentoring</option>
        </select>
      </div>
      <div class="col-md-2"><input type="date" name="date_from" class="form-control"></div>
      <div class="col-md-2"><input type="date" name="date_to" class="form-control"></div>
      <div class="col-md-2"><button class="btn btn-outline-primary w-100" type="submit">Filter</button></div>
    </form>
    <div id='calendar'></div>
    <!-- Event Detail Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eventDetailModalLabel">Event Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="eventDetailContent"></div>
            <div id="eventActionArea" class="mt-3"></div>
            <div id="eventAlertArea"></div>
          </div>
        </div>
      </div>
    </div>
    <?php foreach ($events as $month => $elist): ?>
        <h4 class="mt-4"><?= htmlspecialchars($month) ?></h4>
        <table class="table table-bordered table-striped">
            <thead><tr><th>Title</th><th>Type</th><th>Date/Time</th><th>Location</th><th>Description</th></tr></thead>
            <tbody>
            <?php foreach ($elist as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['title']) ?></td>
                    <td><?= htmlspecialchars($e['event_type']) ?></td>
                    <td><?= date('D, d M Y H:i', strtotime($e['start_datetime'])) ?> - <?= date('H:i', strtotime($e['end_datetime'])) ?></td>
                    <td><?= htmlspecialchars($e['location']) ?></td>
                    <td><?= htmlspecialchars($e['description']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
<script>
let calendar;
function loadEvents(filters = {}) {
  let params = new URLSearchParams(filters).toString();
  fetch('fetch_events.php?' + params)
    .then(res => res.json())
    .then(events => {
      if (calendar) calendar.destroy();
      calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        events: events,
        eventColor: '#007bff',
        eventDidMount: function(info) {
          if (info.event.extendedProps.event_type === 'workshop') info.el.style.backgroundColor = '#28a745';
          if (info.event.extendedProps.event_type === 'training') info.el.style.backgroundColor = '#ffc107';
          if (info.event.extendedProps.event_type === 'mentoring') info.el.style.backgroundColor = '#17a2b8';
        },
        eventClick: function(info) {
          // Show modal with event details
          fetch('event_detail.php?id=' + info.event.id)
            .then(res => res.text())
            .then(html => {
              document.getElementById('eventDetailContent').innerHTML = html;
              // Show registration/invitation actions
              fetch('event_action_area.php?id=' + info.event.id)
                .then(res => res.text())
                .then(html2 => {
                  document.getElementById('eventActionArea').innerHTML = html2;
                });
              new bootstrap.Modal(document.getElementById('eventDetailModal')).show();
            });
        },
        editable: <?= (isset($_SESSION['role']) && $_SESSION['role']==='admin') ? 'true' : 'false' ?>,
        eventDrop: function(info) {
          fetch('update_event_datetime.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: info.event.id, start: info.event.startStr, end: info.event.endStr})
          });
        }
      });
      calendar.render();
    });
}
document.getElementById('eventFilterForm').onsubmit = function(e) {
  e.preventDefault();
  let data = Object.fromEntries(new FormData(this));
  loadEvents(data);
};
loadEvents();
</script>
</body>
</html> 