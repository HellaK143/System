<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die('Access denied.');
require_once 'db.php';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die('DB error: ' . $conn->connect_error);
// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_entrepreneur'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $student_id = trim($_POST['student_id']);
    $department = trim($_POST['department']);
    $course = trim($_POST['course']);
    $year = trim($_POST['year_of_study']);
    $gender = trim($_POST['gender']);
    $profile_picture = trim($_POST['profile_picture']);
    $registration_date = trim($_POST['registration_date']);
    $interests = trim($_POST['interests']);
    $sector = trim($_POST['sector']);
    $role = trim($_POST['role']);
    $stmt = $conn->prepare("INSERT INTO entrepreneur (first_name, last_name, email, phone, student_id, department, course, year_of_study, gender, profile_picture, registration_date, interests, sector, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssssssssss', $first, $last, $email, $phone, $student_id, $department, $course, $year, $gender, $profile_picture, $registration_date, $interests, $sector, $role);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_entrepreneurs_admin.php'); exit;
}
// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_entrepreneur'])) {
    $id = intval($_POST['entrepreneur_id']);
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $student_id = trim($_POST['student_id']);
    $department = trim($_POST['department']);
    $course = trim($_POST['course']);
    $year = trim($_POST['year_of_study']);
    $gender = trim($_POST['gender']);
    $profile_picture = trim($_POST['profile_picture']);
    $registration_date = trim($_POST['registration_date']);
    $interests = trim($_POST['interests']);
    $sector = trim($_POST['sector']);
    $role = trim($_POST['role']);
    $stmt = $conn->prepare("UPDATE entrepreneur SET first_name=?, last_name=?, email=?, phone=?, student_id=?, department=?, course=?, year_of_study=?, gender=?, profile_picture=?, registration_date=?, interests=?, sector=?, role=? WHERE entrepreneur_id=?");
    $stmt->bind_param('ssssssssssssssi', $first, $last, $email, $phone, $student_id, $department, $course, $year, $gender, $profile_picture, $registration_date, $interests, $sector, $role, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_entrepreneurs_admin.php'); exit;
}
// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_entrepreneur'])) {
    $id = intval($_POST['entrepreneur_id']);
    $conn->query("DELETE FROM entrepreneur WHERE entrepreneur_id=$id");
    header('Location: manage_entrepreneurs_admin.php'); exit;
}
$entrepreneurs = $conn->query("SELECT entrepreneur_id, first_name, last_name, email, phone, student_id, department, course, year_of_study, gender, profile_picture, registration_date, interests, sector, role FROM entrepreneur ORDER BY registration_date DESC");
$conn->close();
$page_title = 'Manage Entrepreneurs';
$breadcrumb_items = ['Entrepreneurs', 'Manage Entrepreneurs'];
ob_start();
?>
<div class="container my-5">
    <h2 class="mb-4">Manage Entrepreneurs</h2>
    <a href="export_entrepreneurs.php" class="btn btn-success mb-3">Export as Excel</a>
    <button class="btn btn-primary mb-3 ms-2" data-bs-toggle="modal" data-bs-target="#addEntrepreneurModal">Add New Entrepreneur</button>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search entrepreneurs...">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Student ID</th>
                    <th>Department</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Profile Picture</th>
                    <th>Registration Date</th>
                    <th>Interests</th>
                    <th>Sector</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="First Name"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Last Name"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Email"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Phone"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Student ID"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Department"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Course"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Year"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Gender"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Profile Picture"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Registration Date"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Interests"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Sector"></th>
                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="Role"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entrepreneurs && $entrepreneurs->num_rows > 0): while($e = $entrepreneurs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($e['entrepreneur_id']) ?></td>
                    <td><?= htmlspecialchars($e['first_name']) ?></td>
                    <td><?= htmlspecialchars($e['last_name']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= htmlspecialchars($e['phone']) ?></td>
                    <td><?= htmlspecialchars($e['student_id']) ?></td>
                    <td><?= htmlspecialchars($e['department']) ?></td>
                    <td><?= htmlspecialchars($e['course']) ?></td>
                    <td><?= htmlspecialchars($e['year_of_study']) ?></td>
                    <td><?= htmlspecialchars($e['gender']) ?></td>
                    <td><?php if ($e['profile_picture']) { ?><img src="uploads/<?= htmlspecialchars($e['profile_picture']) ?>" alt="Profile" width="40"><?php } ?></td>
                    <td><?= htmlspecialchars($e['registration_date']) ?></td>
                    <td><?= htmlspecialchars($e['interests']) ?></td>
                    <td><?= htmlspecialchars($e['sector']) ?></td>
                    <td><?= htmlspecialchars($e['role']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editEntrepreneurModal<?= $e['entrepreneur_id'] ?>">Edit</button>
                        <form method="post" action="" style="display:inline-block" onsubmit="return confirm('Delete this entrepreneur?');">
                            <input type="hidden" name="entrepreneur_id" value="<?= $e['entrepreneur_id'] ?>">
                            <button type="submit" name="delete_entrepreneur" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editEntrepreneurModal<?= $e['entrepreneur_id'] ?>" tabindex="-1" aria-labelledby="editEntrepreneurModalLabel<?= $e['entrepreneur_id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editEntrepreneurModalLabel<?= $e['entrepreneur_id'] ?>">Edit Entrepreneur</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="entrepreneur_id" value="<?= $e['entrepreneur_id'] ?>">
                          <div class="mb-3"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($e['first_name']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($e['last_name']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($e['email']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($e['phone']) ?>" required></div>
                          <div class="mb-3"><label class="form-label">Student ID</label><input type="text" name="student_id" class="form-control" value="<?= htmlspecialchars($e['student_id']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="<?= htmlspecialchars($e['department']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Course</label><input type="text" name="course" class="form-control" value="<?= htmlspecialchars($e['course']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Year of Study</label><input type="number" name="year_of_study" class="form-control" value="<?= htmlspecialchars($e['year_of_study']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="male" <?= $e['gender']==='male'?'selected':'' ?>>Male</option><option value="female" <?= $e['gender']==='female'?'selected':'' ?>>Female</option><option value="other" <?= $e['gender']==='other'?'selected':'' ?>>Other</option></select></div>
                          <div class="mb-3"><label class="form-label">Profile Picture</label><input type="text" name="profile_picture" class="form-control" value="<?= htmlspecialchars($e['profile_picture']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Registration Date</label><input type="datetime-local" name="registration_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($e['registration_date'])) ?>"></div>
                          <div class="mb-3"><label class="form-label">Interests</label><input type="text" name="interests" class="form-control" value="<?= htmlspecialchars($e['interests']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Sector</label><input type="text" name="sector" class="form-control" value="<?= htmlspecialchars($e['sector']) ?>"></div>
                          <div class="mb-3"><label class="form-label">Role</label><input type="text" name="role" class="form-control" value="<?= htmlspecialchars($e['role']) ?>"></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" name="edit_entrepreneur" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; else: ?>
                <tr><td colspan="16" class="text-center text-muted">No entrepreneurs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addEntrepreneurModal" tabindex="-1" aria-labelledby="addEntrepreneurModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addEntrepreneurModalLabel">Add New Entrepreneur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Student ID</label><input type="text" name="student_id" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Course</label><input type="text" name="course" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Year of Study</label><input type="number" name="year_of_study" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
          <div class="mb-3"><label class="form-label">Profile Picture</label><input type="text" name="profile_picture" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Registration Date</label><input type="datetime-local" name="registration_date" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Interests</label><input type="text" name="interests" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Sector</label><input type="text" name="sector" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Role</label><input type="text" name="role" class="form-control"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_entrepreneur" class="btn btn-primary">Add Entrepreneur</button>
        </div>
      </form>
    </div>
  </div>
</div>
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