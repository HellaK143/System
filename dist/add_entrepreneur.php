<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic"; // replace with your database

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $student_id = $_POST['student_id'];
    $department = $_POST['department'];
    $course = $_POST['course'];
    $role = $_POST['role'];
    $sector = $_POST['sector'];
    $interests = $_POST['interests'];
    $year_of_study = $_POST['year_of_study'];
    $gender = $_POST['gender'];
    $profile_picture = "";
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }

    // Handle image upload
    if (!empty($_FILES["profile_picture"]["name"])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = basename($target_file);
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (empty($error)) {
        // DEBUG: Show password before insert
        echo '<pre>DEBUG: Password to insert: ' . htmlspecialchars($password) . '</pre>';
        $stmt = $conn->prepare("INSERT INTO entrepreneur (first_name, last_name, email, phone, student_id, department, course, year_of_study, gender, profile_picture, password, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssssssss", $first_name, $last_name, $email, $phone, $student_id, $department, $course, $year_of_study, $gender, $profile_picture, $password);

        if ($stmt->execute()) {
            $success = "Entrepreneur registered successfully!";
        } else {
            $error = "Error: " . $stmt->error;
            // DEBUG: Show error
            echo '<pre>DEBUG: MySQL error: ' . htmlspecialchars($stmt->error) . '</pre>';
        }

        $stmt->close();
    }
}

$conn->close();

$page_title = "Add Entrepreneur";
$breadcrumb_items = ["Entrepreneurs", "Add Entrepreneur"];
$additional_css = "
    .card { border-radius: 1rem; }
    .form-label { font-weight: 600; }
";

ob_start();
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-user-plus"></i> Register New Entrepreneur
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success"> <?= htmlspecialchars($success) ?> </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student ID</label>
                                <input type="text" name="student_id" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <input type="text" name="course" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year of Study</label>
                                <input type="text" name="year_of_study" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" name="role" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sector</label>
                                <input type="text" name="sector" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Interests</label>
                                <input type="text" name="interests" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'template_admin.php';
