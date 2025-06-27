<?php
session_start();
require_once 'db.php';
secure_session_check();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);

// Fetch mentors for assignment dropdown
$mentors = [];
$res = $conn->query("SELECT mentor_id, full_name FROM mentors ORDER BY full_name");
while ($r = $res->fetch_assoc()) $mentors[] = $r;

// Helper: Send email
function send_notification_email($to, $subject, $body) {
    $headers = "From: noreply@umu.ac.ug\r\nContent-Type: text/plain; charset=UTF-8";
    @mail($to, $subject, $body, $headers);
}
// Helper: Send in-system message
function send_in_system_message($application_id, $sender, $recipient, $message) {
    global $host, $user, $password, $dbname;
    $conn = new mysqli($host, $user, $password, $dbname);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("INSERT INTO messages (application_id, sender, recipient, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('isss', $application_id, $sender, $recipient, $message);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $app_id = intval($_POST['application_id']);
    $new_status = trim($_POST['status']);
    $conn = new mysqli($host, $user, $password, $dbname);
    $app = $conn->query("SELECT * FROM applications WHERE id=$app_id")->fetch_assoc();
    $mentor = null;
    if ($app['assigned_mentor']) {
        $mentor = $conn->query("SELECT * FROM mentors WHERE mentor_id=".intval($app['assigned_mentor']))->fetch_assoc();
    }
    $feedback = $app['feedback'] ?? '';
    $conn->query("UPDATE applications SET status='".$conn->real_escape_string($new_status)."' WHERE id=$app_id");
    // Compose applicant notification
    $applicant_email = $app['email'];
    $applicant_name = $app['full_name'];
    $mentor_name = $mentor ? $mentor['full_name'] : 'Not yet assigned';
    $mentor_email = $mentor ? $mentor['email'] : '';
    $subject = $body = '';
    if ($new_status === 'Under Review') {
        $subject = 'Your Application is Under Review';
        $body = "Dear $applicant_name,\n\nYour application (ID: $app_id) is currently under review. We will notify you once a decision is made.\n\nAssigned Mentor: $mentor_name\nFeedback: ".($feedback ?: 'No feedback yet')."\n\nRegards,\nUMU Innovation Office";
    } elseif ($new_status === 'Submitted' || $new_status === 'Pending') {
        $subject = 'Your Application is Pending';
        $body = "Dear $applicant_name,\n\nYour application (ID: $app_id) is pending further action. Please check your portal for updates.\n\nAssigned Mentor: $mentor_name\nFeedback: ".($feedback ?: 'No feedback yet')."\n\nRegards,\nUMU Innovation Office";
    } elseif ($new_status === 'Rejected') {
        $subject = 'Your Application Status: Rejected';
        $body = "Dear $applicant_name,\n\nWe regret to inform you that your application (ID: $app_id) has been rejected.\n\nAssigned Mentor: $mentor_name\nFeedback: ".($feedback ?: 'No feedback provided')."\n\nRegards,\nUMU Innovation Office";
    } elseif ($new_status === 'Accepted' || $new_status === 'Shortlisted') {
        $subject = 'Your Application Update';
        $body = "Dear $applicant_name,\n\nCongratulations! Your application (ID: $app_id) has been $new_status.\n\nAssigned Mentor: $mentor_name\nFeedback: ".($feedback ?: 'No feedback yet')."\n\nRegards,\nUMU Innovation Office";
    }
    if ($subject && $body) {
        send_notification_email($applicant_email, $subject, $body);
        send_in_system_message($app_id, $_SESSION['email'] ?? 'system', $applicant_email, $body);
    }
    require_once 'db.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Status Updated', "Status of application $app_id set to $new_status");
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'application', 'Status Updated', 'Status for application #' . $app_id . ' set to ' . $new_status);
    }
    // Notify applicant (entrepreneur)
    $applicant_user_id = get_user_id_by_email($applicant_email);
    if ($applicant_user_id) {
        add_notification($applicant_user_id, 'info', 'application', 'Application Status Updated', 'Your application status is now: ' . $new_status);
    }
    // Notify mentor if assigned
    if ($mentor && !empty($mentor_email)) {
        $mentor_user_id = get_user_id_by_email($mentor_email);
        if ($mentor_user_id) {
            add_notification($mentor_user_id, 'info', 'application', 'Application Assigned Status Changed', 'Status for your assigned application #' . $app_id . ' is now: ' . $new_status);
        }
    }
    $conn->close();
    header('Location: manage_applications_admin.php'); exit;
}
// Handle mentor assignment (add notification)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_mentor'])) {
    $app_id = intval($_POST['application_id']);
    $mentor_id = intval($_POST['mentor_id']);
    $conn = new mysqli($host, $user, $password, $dbname);
    
    // Get mentor details first
    $mentor = $conn->query("SELECT * FROM mentors WHERE mentor_id=$mentor_id")->fetch_assoc();
    $mentor_email = $mentor ? $mentor['email'] : null;
    
    // Update both assigned_mentor and assigned_mentor_email
    $conn->query("UPDATE applications SET assigned_mentor=$mentor_id, assigned_mentor_email='" . $conn->real_escape_string($mentor_email) . "' WHERE id=$app_id");
    
    $app = $conn->query("SELECT * FROM applications WHERE id=$app_id")->fetch_assoc();
    // Notify mentor
    $mentor_name = $mentor ? $mentor['full_name'] : 'Unknown';
    $applicant_name = $app['full_name'];
    $applicant_email = $app['email'];
    $subject = 'New Application Assigned to You';
    $body = "Dear $mentor_name,\n\nYou have been assigned to mentor the following application:\n\nApplication ID: $app_id\nApplicant Name: $applicant_name\nApplicant Email: $applicant_email\n\nPlease log in to your dashboard to view more details.\n\nRegards,\nUMU Innovation Office";
    send_notification_email($mentor_email, $subject, $body);
    send_in_system_message($app_id, $_SESSION['email'] ?? 'system', $mentor_email, $body);
    require_once 'db.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Mentor Assigned', 'Mentor ID ' . $mentor_id . ' assigned to application ' . $app_id);
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'success', 'mentor', 'Mentor Assigned', 'Mentor ' . $mentor_name . ' assigned to application #' . $app_id);
    }
    // Notify mentor
    $mentor_user_id = get_user_id_by_email($mentor_email);
    if ($mentor_user_id) {
        add_notification($mentor_user_id, 'success', 'mentor', 'You Have Been Assigned an Application', 'You have been assigned to application #' . $app_id . ' (' . $applicant_name . ')');
    }
    // Notify applicant
    $applicant_user_id = get_user_id_by_email($applicant_email);
    if ($applicant_user_id) {
        add_notification($applicant_user_id, 'info', 'mentor', 'Mentor Assigned', 'A mentor (' . $mentor_name . ') has been assigned to your application #' . $app_id);
    }
    $conn->close();
    header('Location: manage_applications_admin.php'); exit;
}
// Handle feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_feedback'])) {
    $app_id = intval($_POST['application_id']);
    $feedback = trim($_POST['feedback']);
    $stmt = $conn->prepare("UPDATE applications SET feedback=? WHERE id=?");
    $stmt->bind_param('si', $feedback, $app_id);
    $stmt->execute();
    $stmt->close();
    require_once 'db.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Feedback Sent', 'Feedback sent for application ' . $app_id);
    // Notify all admins
    foreach (get_user_ids_by_role('admin') as $admin_id) {
        add_notification($admin_id, 'info', 'application', 'Feedback Sent', 'Feedback sent for application #' . $app_id);
    }
    // Notify applicant
    $app = $conn->query("SELECT * FROM applications WHERE id=$app_id")->fetch_assoc();
    $applicant_email = $app['email'];
    $applicant_user_id = get_user_id_by_email($applicant_email);
    if ($applicant_user_id) {
        add_notification($applicant_user_id, 'info', 'application', 'Feedback Received', 'Feedback was sent for your application #' . $app_id);
    }
    header('Location: manage_applications_admin.php'); exit;
}
// Handle message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $recipient_email = trim($_POST['recipient_email']);
    $message = trim($_POST['message']);
    $application_id = intval($_POST['application_id'] ?? 0);
    $sender = $_SESSION['email'] ?? 'system';
    $conn2 = new mysqli($host, $user, $password, $dbname);
    $stmt = $conn2->prepare("INSERT INTO messages (application_id, sender, recipient, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param('isss', $application_id, $sender, $recipient_email, $message);
    $stmt->execute();
    $stmt->close();
    $conn2->close();
    require_once 'db.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Message Sent', 'Message sent to ' . $recipient_email . ' for application ' . $application_id);
    header('Location: manage_applications_admin.php'); exit;
}
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_application'])) {
    $name = trim($_POST['full_name']);
    $category = trim($_POST['category']);
    $status = trim($_POST['status']);
    $submitted = trim($_POST['submitted_at']);
    $stmt = $conn->prepare("INSERT INTO applications (full_name, category, status, submitted_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $category, $status, $submitted);
    $stmt->execute();
    $stmt->close();
    require_once 'db.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Application Modified', 'Application added by admin.');
    header('Location: manage_applications_admin.php'); exit;
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_application'])) {
    $id = intval($_POST['application_id']);
    $name = trim($_POST['full_name']);
    $category = trim($_POST['category']);
    $status = trim($_POST['status']);
    $submitted = trim($_POST['submitted_at']);
    $stmt = $conn->prepare("UPDATE applications SET full_name=?, category=?, status=?, submitted_at=? WHERE id=?");
    $stmt->bind_param('ssssi', $name, $category, $status, $submitted, $id);
    $stmt->execute();
    $stmt->close();
    require_once 'db.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], 'Application Modified', 'Application edited by admin.');
    header('Location: manage_applications_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_application'])) {
    $id = intval($_POST['application_id']);
    $conn->query("DELETE FROM applications WHERE id=$id");
    header('Location: manage_applications_admin.php'); exit;
}
// Filters
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$where = [];
if ($status_filter) $where[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
if ($date_from) $where[] = "DATE(submitted_at) >= '" . $conn->real_escape_string($date_from) . "'";
if ($date_to) $where[] = "DATE(submitted_at) <= '" . $conn->real_escape_string($date_to) . "'";
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$apps = $conn->query("SELECT * FROM applications $where_sql ORDER BY submitted_at DESC");
// Fetch all users, mentors, entrepreneurs, evaluators for messaging
$all_users = $conn->query("SELECT email, username, role FROM users");
$all_mentors = $conn->query("SELECT email, full_name FROM mentors");
$all_entrepreneurs = $conn->query("SELECT email, first_name, last_name FROM entrepreneur");
$all_evaluators = $conn->query("SELECT email, username FROM users WHERE role='evaluator'");
$conn->close();
$page_title = 'Manage Applications';
$breadcrumb_items = ['Applications', 'Manage Applications'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Applications</h2>
    <a href="export_applications.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addApplicationModal">Add New Application</button>
    <form class="row g-3 mb-3" method="get">
        <div class="col-md-3">
            <label for="date_from" class="form-label">Date From</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">Date To</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All</option>
                <option value="Submitted"<?= $status_filter === 'Submitted' ? ' selected' : '' ?>>Submitted</option>
                <option value="Under Review"<?= $status_filter === 'Under Review' ? ' selected' : '' ?>>Under Review</option>
                <option value="Shortlisted"<?= $status_filter === 'Shortlisted' ? ' selected' : '' ?>>Shortlisted</option>
                <option value="Rejected"<?= $status_filter === 'Rejected' ? ' selected' : '' ?>>Rejected</option>
                <option value="Accepted"<?= $status_filter === 'Accepted' ? ' selected' : '' ?>>Accepted</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
    <div class="table-responsive" style="max-width:100vw;overflow-x:auto;">
        <table id="dataTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Program</th>
                    <th>Full Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    <th>Assigned Mentor</th>
                    <th>Campus</th>
                    <th>Student Number</th>
                    <th>Course</th>
                    <th>Year of Study</th>
                    <th>Graduation Year</th>
                    <th>Current Job</th>
                    <th>Employer</th>
                    <th>Staff Number</th>
                    <th>Faculty</th>
                    <th>Years at UMU</th>
                    <th>National ID</th>
                    <th>Occupation</th>
                    <th>Marital Status</th>
                    <th>Num Beneficiaries</th>
                    <th>Nationality</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Street</th>
                    <th>Village</th>
                    <th>Subcounty</th>
                    <th>Country</th>
                    <th>District</th>
                    <th>Refugee</th>
                    <th>Age Range</th>
                    <th>Disability</th>
                    <th>Disability Text</th>
                    <th>Gender</th>
                    <th>Business Idea Name</th>
                    <th>Sector</th>
                    <th>Program Attended</th>
                    <th>Initial Capital</th>
                    <th>Cohort</th>
                    <th>Year of Inception</th>
                    <th>Interested In</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial = 1;
                if ($apps && $apps->num_rows > 0): while($a = $apps->fetch_assoc()): ?>
                <tr>
                    <td><?= $serial++ ?></td>
                    <td><?= htmlspecialchars($a['id']) ?></td>
                    <td><?= htmlspecialchars($a['program']) ?></td>
                    <td><?= htmlspecialchars($a['full_name']) ?></td>
                    <td><?= htmlspecialchars($a['category']) ?></td>
                    <td>
                        <form method="post" class="d-flex align-items-center gap-2" style="min-width:180px">
                            <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                            <select name="status" class="form-select form-select-sm" style="width:auto;display:inline-block">
                                <option value="Submitted"<?= $a['status']==='Submitted'?' selected':'' ?>>Submitted</option>
                                <option value="Under Review"<?= $a['status']==='Under Review'?' selected':'' ?>>Under Review</option>
                                <option value="Shortlisted"<?= $a['status']==='Shortlisted'?' selected':'' ?>>Shortlisted</option>
                                <option value="Rejected"<?= $a['status']==='Rejected'?' selected':'' ?>>Rejected</option>
                                <option value="Accepted"<?= $a['status']==='Accepted'?' selected':'' ?>>Accepted</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-outline-success btn-sm">Save</button>
                        </form>
                    </td>
                    <td>
                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal<?= $a['id'] ?>">Feedback</button>
                        <!-- Feedback Modal -->
                        <div class="modal fade" id="feedbackModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="feedbackModalLabel<?= $a['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post">
                              <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="feedbackModalLabel<?= $a['id'] ?>">Send Feedback</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <textarea name="feedback" class="form-control" rows="4" required><?= htmlspecialchars($a['feedback']) ?></textarea>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" name="send_feedback" class="btn btn-success">Send</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                    </td>
                    <td>
                        <form method="post" class="d-flex align-items-center gap-2" style="min-width:180px">
                            <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                            <select name="mentor_id" class="form-select form-select-sm" style="width:auto;display:inline-block">
                                <option value="">Assign Mentor</option>
                                <?php foreach ($mentors as $m): ?>
                                    <option value="<?= $m['mentor_id'] ?>" <?= ($a['assigned_mentor']==$m['mentor_id'])?'selected':'' ?>><?= htmlspecialchars($m['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assign_mentor" class="btn btn-outline-primary btn-sm">Assign</button>
                        </form>
                    </td>
                    <td><?= htmlspecialchars($a['campus']) ?></td>
                    <td><?= htmlspecialchars($a['student_number']) ?></td>
                    <td><?= htmlspecialchars($a['course']) ?></td>
                    <td><?= htmlspecialchars($a['year_of_study']) ?></td>
                    <td><?= htmlspecialchars($a['graduation_year']) ?></td>
                    <td><?= htmlspecialchars($a['current_job']) ?></td>
                    <td><?= htmlspecialchars($a['employer']) ?></td>
                    <td><?= htmlspecialchars($a['staff_number']) ?></td>
                    <td><?= htmlspecialchars($a['faculty']) ?></td>
                    <td><?= htmlspecialchars($a['years_at_umu']) ?></td>
                    <td><?= htmlspecialchars($a['national_id']) ?></td>
                    <td><?= htmlspecialchars($a['occupation']) ?></td>
                    <td><?= htmlspecialchars($a['marital_status']) ?></td>
                    <td><?= htmlspecialchars($a['num_beneficiaries']) ?></td>
                    <td><?= htmlspecialchars($a['nationality']) ?></td>
                    <td><?= htmlspecialchars($a['phone']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td><?= htmlspecialchars($a['street']) ?></td>
                    <td><?= htmlspecialchars($a['village']) ?></td>
                    <td><?= htmlspecialchars($a['subcounty']) ?></td>
                    <td><?= htmlspecialchars($a['country']) ?></td>
                    <td><?= htmlspecialchars($a['district']) ?></td>
                    <td><?= htmlspecialchars($a['refugee']) ?></td>
                    <td><?= htmlspecialchars($a['age_range']) ?></td>
                    <td><?= htmlspecialchars($a['disability']) ?></td>
                    <td><?= htmlspecialchars($a['disability_text']) ?></td>
                    <td><?= htmlspecialchars($a['gender']) ?></td>
                    <td><?= htmlspecialchars($a['business_idea_name']) ?></td>
                    <td><?= htmlspecialchars($a['sector']) ?></td>
                    <td><?= htmlspecialchars($a['program_attended']) ?></td>
                    <td><?= htmlspecialchars($a['initial_capital']) ?></td>
                    <td><?= htmlspecialchars($a['cohort']) ?></td>
                    <td><?= htmlspecialchars($a['year_of_inception']) ?></td>
                    <td><?= htmlspecialchars($a['interested_in']) ?></td>
                    <td><?= htmlspecialchars($a['submitted_at']) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?= $a['id'] ?>">Message</button>
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editApplicationModal<?= $a['id'] ?>">Edit</button>
                            <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this application?');">
                                <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                                <button type="submit" name="delete_application" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </div>
                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $a['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="post">
                              <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="messageModalLabel<?= $a['id'] ?>">Send Message</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <label class="form-label">Recipient</label>
                                  <select name="recipient_email" class="form-select mb-3" required>
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
                                  <label class="form-label">Message</label>
                                  <textarea name="message" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" name="send_message" class="btn btn-primary">Send</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editApplicationModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="editApplicationModalLabel<?= $a['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form method="post" action="">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="editApplicationModalLabel<?= $a['id'] ?>">Edit Application</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                                  <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($a['full_name']) ?>" required>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($a['category']) ?>" required>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($a['status']) ?>" required>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Submitted</label>
                                    <input type="datetime-local" name="submitted_at" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($a['submitted_at'])) ?>" required>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" name="edit_application" class="btn btn-primary">Save Changes</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="44" class="text-center text-muted">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addApplicationModal" tabindex="-1" aria-labelledby="addApplicationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addApplicationModalLabel">Add New Application</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <input type="text" name="status" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Submitted</label>
            <input type="datetime-local" name="submitted_at" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_application" class="btn btn-primary">Add Application</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Mentor Assignment Note for Admins -->
<div class="alert alert-info mb-3">To assign a mentor, select from the list below. If a mentor does not appear, add them to the mentors table first.</div>
<script>
// Global search
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(value) > -1 ? '' : 'none';
    });
});
// Column-specific search
const columnInputs = document.querySelectorAll('.column-search');
columnInputs.forEach(function(input, colIdx) {
    input.addEventListener('keyup', function() {
        var value = this.value.toLowerCase();
        var rows = document.querySelectorAll('#dataTable tbody tr');
        rows.forEach(function(row) {
            var cell = row.querySelectorAll('td')[colIdx+1]; // +1 to skip ID
            if (!cell) return;
            var text = cell.textContent.toLowerCase();
            row.style.display = text.indexOf(value) > -1 ? '' : 'none';
        });
    });
});
</script>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 