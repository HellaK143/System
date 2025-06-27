<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
$apps = $conn->query("SELECT program, full_name, category, status, feedback, assigned_mentor, campus, student_number, course, year_of_study, graduation_year, current_job, employer, staff_number, faculty, years_at_umu, national_id, occupation, marital_status, num_beneficiaries, nationality, phone, email, street, village, subcounty, country, district, refugee, age_range, disability, disability_text, gender, business_idea_name, sector, program_attended, initial_capital, cohort, year_of_inception, interested_in, submitted_at FROM applications ORDER BY submitted_at DESC");
$conn->close();
$page_title = 'View Applications';
$breadcrumb_items = ['Applications', 'View Applications'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Applications</h2>
    <a href="export_applications.php" class="btn btn-success mb-3">Export as Excel</a>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search applications...">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped table-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Program</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    <th>Mentor</th>
                    <th>Campus</th>
                    <th>Student #</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Grad Year</th>
                    <th>Current Job</th>
                    <th>Employer</th>
                    <th>Staff #</th>
                    <th>Faculty</th>
                    <th>Years@UMU</th>
                    <th>National ID</th>
                    <th>Occupation</th>
                    <th>Marital</th>
                    <th>Beneficiaries</th>
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
                    <th>Business Idea</th>
                    <th>Sector</th>
                    <th>Program Attended</th>
                    <th>Initial Capital</th>
                    <th>Cohort</th>
                    <th>Year of Inception</th>
                    <th>Interested In</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial = 1;
                if ($apps && $apps->num_rows > 0): while($a = $apps->fetch_assoc()): ?>
                <tr>
                    <td><?= $serial++ ?></td>
                    <td><?= htmlspecialchars($a['program']) ?></td>
                    <td><?= htmlspecialchars($a['full_name']) ?></td>
                    <td><?= htmlspecialchars($a['category']) ?></td>
                    <td><?= htmlspecialchars($a['status']) ?></td>
                    <td><?= htmlspecialchars($a['feedback']) ?></td>
                    <td>
                    <?php
                    $mentor_name = '-';
                    if (!empty($a['assigned_mentor'])) {
                        $mentor_id = intval($a['assigned_mentor']);
                        $mentor_res = $conn->query("SELECT full_name FROM mentors WHERE mentor_id = $mentor_id");
                        if ($mentor_res && $m = $mentor_res->fetch_assoc()) {
                            $mentor_name = $m['full_name'];
                        } else {
                            // Try users table
                            $user_res = $conn->query("SELECT username FROM users WHERE user_id = $mentor_id AND role = 'mentor'");
                            if ($user_res && $u = $user_res->fetch_assoc()) {
                                $mentor_name = $u['username'];
                            } else {
                                $mentor_name = $mentor_id;
                            }
                        }
                    }
                    echo htmlspecialchars($mentor_name);
                    ?>
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
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="42" class="text-center text-muted">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(value) > -1 ? '' : 'none';
    });
});
</script>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/dist/template_admin.php'; 