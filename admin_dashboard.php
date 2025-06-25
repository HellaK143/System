<?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="dist/schedule_session.php" class="btn btn-primary">Schedule Session</a>
<?php endif; ?>
<a href="dist/sessions_list.php" class="btn btn-info">My Sessions</a>
<a href="dist/events_calendar.php" class="btn btn-info mb-2">Event Calendar</a>
<a href="dist/resources.php" class="btn btn-info mb-2">Resources</a>
<a href="dist/bookings.php" class="btn btn-info mb-2">My Bookings</a>
<a href="dist/admin_bookings.php" class="btn btn-warning mb-2">Booking Approvals</a> 