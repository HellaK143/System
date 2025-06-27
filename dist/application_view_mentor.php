<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error');
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
if (!$user_id || $role !== 'mentor') die('Access denied.');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die('Invalid application ID.');
$app_id = intval($_GET['id']);
// Robust mentor lookup
$mentor_id = null;
$mentor_res = $conn->query("SELECT mentor_id FROM mentors WHERE user_id = $user_id");
if ($mentor_res && $m = $mentor_res->fetch_assoc()) {
    $mentor_id = $m['mentor_id'];
} else {
    // Fallback: try to match by email
    $user_email = '';
    $user_res = $conn->query("SELECT email FROM users WHERE user_id = $user_id");
    if ($user_res && $u = $user_res->fetch_assoc()) {
        $user_email = strtolower(trim($u['email']));
        $mentor_res2 = $conn->query("SELECT mentor_id FROM mentors WHERE LOWER(TRIM(email)) = '" . $conn->real_escape_string($user_email) . "'");
        if ($mentor_res2 && $m2 = $mentor_res2->fetch_assoc()) {
            $mentor_id = $m2['mentor_id'];
        }
    }
}
if (!$mentor_id) die('You are not yet registered as a mentor in the system. Please contact the administrator.');
$stmt = $conn->prepare("SELECT * FROM applications WHERE id = ? AND assigned_mentor = ?");
$stmt->bind_param("ii", $app_id, $mentor_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die('Application not found or not assigned to you.');
$app = $res->fetch_assoc();
$stmt->close();
$conn->close();
// Fetch mentor name for display
$mentor_name = '';
if (!empty($app['assigned_mentor'])) {
    $conn2 = new mysqli($host, $user, $password, $dbname);
    if (!$conn2->connect_error) {
        $mid = intval($app['assigned_mentor']);
        $mres = $conn2->query("SELECT full_name FROM mentors WHERE mentor_id = $mid");
        if ($mres && $mrow = $mres->fetch_assoc()) {
            $mentor_name = $mrow['full_name'];
        } else {
            $mentor_name = $app['assigned_mentor'];
        }
        $conn2->close();
    }
}
// Fetch evaluator name for display
$evaluator_name = '';
if (!empty($app['assigned_evaluator']) && intval($app['assigned_evaluator']) > 0) {
    $conn3 = new mysqli($host, $user, $password, $dbname);
    if (!$conn3->connect_error) {
        $eid = intval($app['assigned_evaluator']);
        $eres = $conn3->query("SELECT username FROM users WHERE user_id = $eid");
        if ($eres && $erow = $eres->fetch_assoc()) {
            $evaluator_name = $erow['username'];
        } else {
            $evaluator_name = $app['assigned_evaluator'];
        }
        $conn3->close();
    }
}
$page_title = 'View Application';
$breadcrumb_items = ['View Application'];
ob_start();
?>
<div class="container my-5">
    <?php if (!empty($_SESSION['msg_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['msg_success']) ?></div>
        <?php unset($_SESSION['msg_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['msg_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['msg_error']) ?></div>
        <?php unset($_SESSION['msg_error']); ?>
    <?php endif; ?>
    <h2>Application Details</h2>
    <table class="table table-bordered">
        <tr><th>ID</th><td><?= $app['id'] ?></td></tr>
        <tr><th>Program ID</th><td><?= htmlspecialchars($app['program_id']) ?></td></tr>
        <tr><th>Program</th><td><?= htmlspecialchars($app['program']) ?></td></tr>
        <tr><th>Applicant Name</th><td><?= htmlspecialchars($app['full_name']) ?></td></tr>
        <tr><th>Category</th><td><?= htmlspecialchars($app['category']) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($app['status']) ?></td></tr>
        <tr><th>Feedback</th><td><?= !empty($app['feedback']) ? nl2br(htmlspecialchars($app['feedback'])) : '<em>No feedback available</em>' ?></td></tr>
        <tr><th>Assigned Mentor</th><td><?= htmlspecialchars($mentor_name) ?></td></tr>
        <tr><th>Assigned Mentor Email</th><td><?= htmlspecialchars($app['assigned_mentor_email']) ?></td></tr>
        <tr><th>Campus</th><td><?= htmlspecialchars($app['campus']) ?></td></tr>
        <tr><th>Student Number</th><td><?= htmlspecialchars($app['student_number']) ?></td></tr>
        <tr><th>Course</th><td><?= htmlspecialchars($app['course']) ?></td></tr>
        <tr><th>Year of Study</th><td><?= htmlspecialchars($app['year_of_study']) ?></td></tr>
        <tr><th>Graduation Year</th><td><?= htmlspecialchars($app['graduation_year']) ?></td></tr>
        <tr><th>Current Job</th><td><?= htmlspecialchars($app['current_job']) ?></td></tr>
        <tr><th>Employer</th><td><?= htmlspecialchars($app['employer']) ?></td></tr>
        <tr><th>Staff Number</th><td><?= htmlspecialchars($app['staff_number']) ?></td></tr>
        <tr><th>Faculty</th><td><?= htmlspecialchars($app['faculty']) ?></td></tr>
        <tr><th>Years at UMU</th><td><?= htmlspecialchars($app['years_at_umu']) ?></td></tr>
        <tr><th>National ID</th><td><?= htmlspecialchars($app['national_id']) ?></td></tr>
        <tr><th>Occupation</th><td><?= htmlspecialchars($app['occupation']) ?></td></tr>
        <tr><th>Marital Status</th><td><?= htmlspecialchars($app['marital_status']) ?></td></tr>
        <tr><th>Number of Beneficiaries</th><td><?= htmlspecialchars($app['num_beneficiaries']) ?></td></tr>
        <tr><th>Nationality</th><td><?= htmlspecialchars($app['nationality']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($app['phone']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($app['email']) ?></td></tr>
        <tr><th>Street</th><td><?= htmlspecialchars($app['street']) ?></td></tr>
        <tr><th>Village</th><td><?= htmlspecialchars($app['village']) ?></td></tr>
        <tr><th>Subcounty</th><td><?= htmlspecialchars($app['subcounty']) ?></td></tr>
        <tr><th>Country</th><td><?= htmlspecialchars($app['country']) ?></td></tr>
        <tr><th>District</th><td><?= htmlspecialchars($app['district']) ?></td></tr>
        <tr><th>Refugee</th><td><?= htmlspecialchars($app['refugee']) ?></td></tr>
        <tr><th>Age Range</th><td><?= htmlspecialchars($app['age_range']) ?></td></tr>
        <tr><th>Disability</th><td><?= htmlspecialchars($app['disability']) ?></td></tr>
        <tr><th>Disability Text</th><td><?= htmlspecialchars($app['disability_text']) ?></td></tr>
        <tr><th>Gender</th><td><?= htmlspecialchars($app['gender']) ?></td></tr>
        <tr><th>Business Idea Name</th><td><?= htmlspecialchars($app['business_idea_name']) ?></td></tr>
        <tr><th>Description</th><td><?= isset($app['description']) ? nl2br(htmlspecialchars($app['description'])) : '<em>No description available</em>' ?></td></tr>
        <tr><th>Sector</th><td><?= htmlspecialchars($app['sector']) ?></td></tr>
        <tr><th>Program Attended</th><td><?= htmlspecialchars($app['program_attended']) ?></td></tr>
        <tr><th>Initial Capital</th><td><?= htmlspecialchars($app['initial_capital']) ?></td></tr>
        <tr><th>Cohort</th><td><?= htmlspecialchars($app['cohort']) ?></td></tr>
        <tr><th>Year of Inception</th><td><?= htmlspecialchars($app['year_of_inception']) ?></td></tr>
        <tr><th>Interested In</th><td><?= htmlspecialchars($app['interested_in']) ?></td></tr>
        <tr><th>Submitted At</th><td><?= htmlspecialchars($app['submitted_at']) ?></td></tr>
    </table>
    <form method="post" action="../send_message.php" class="mt-4">
        <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
        <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($app['email']) ?>">
        <div class="mb-3">
            <label for="message" class="form-label">Send Message to Applicant</label>
            <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
    <a href="mentor_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
<?php
$page_content = ob_get_clean();
include 'template_mentor.php'; 