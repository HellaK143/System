<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all mentors for dropdowns
$mentors = [];
$mentors_conn = new mysqli($host, $user, $password, $dbname);
if (!$mentors_conn->connect_error) {
    $res = $mentors_conn->query("SELECT mentor_id, full_name FROM mentors ORDER BY full_name");
    while ($r = $res->fetch_assoc()) $mentors[] = $r;
    $mentors_conn->close();
}

// Handle filters
$email_filter = isset($_GET['email']) ? $_GET['email'] : '';
$mentor_id = null;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mentor') {
    $user_id = $_SESSION['user_id'];
    $user_res = $conn->query("SELECT email FROM users WHERE user_id = $user_id");
    $mentor_email = '';
    if ($user_res && $u = $user_res->fetch_assoc()) $mentor_email = $u['email'];
    if ($mentor_email) {
        $m_res = $conn->query("SELECT mentor_id FROM mentors WHERE email = '" . $conn->real_escape_string($mentor_email) . "'");
        if ($m_res && $m = $m_res->fetch_assoc()) $mentor_id = $m['mentor_id'];
    }
}
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$where = [];
$params = [];
$types = '';
if ($email_filter && $mentor_id) {
    $where[] = 'email = ?';
    $params[] = $email_filter;
    $types .= 's';
    $where[] = 'assigned_mentor = ?';
    $params[] = $mentor_id;
    $types .= 'i';
} else {
    if ($status_filter && $status_filter !== 'all') {
        $where[] = 'status = ?';
        $params[] = $status_filter;
        $types .= 's';
    }
    if ($date_from) {
        $where[] = "DATE(submission_date) >= ?";
        $params[] = $date_from;
        $types .= 's';
    }
    if ($date_to) {
        $where[] = "DATE(submission_date) <= ?";
        $params[] = $date_to;
        $types .= 's';
    }
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT * FROM applications $where_sql ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
        }
        .application-card {
            margin-bottom: 1.5rem;
            max-width: 500px;
            min-width: 280px;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            background: #fff;
        }
        .application-card .card-header {
            background: linear-gradient(90deg, #1a1a1a 60%, #ff0000 100%);
            font-weight: bold;
            font-size: 1.1rem;
            padding: 1rem 1.25rem;
            letter-spacing: 0.5px;
        }
        .application-card .card-body {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            background: #fff;
        }
        .application-fields {
            font-size: 0.98rem;
        }
        .pill-row {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
        }
        .pill-row .field-label {
            font-weight: 600;
            color: #555;
            min-width: 110px;
            max-width: 150px;
            flex: 0 0 120px;
        }
        .pill-row .field-value {
            color: #222;
            margin-left: 1.2rem;
            flex: 1 1 0%;
            word-break: break-word;
        }
        .application-actions {
            margin-top: 1rem;
        }
        .application-actions a.btn {
            min-width: 100px;
            margin-left: 0.5rem;
            margin-bottom: 0.5rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .application-actions a.btn:hover {
            box-shadow: 0 2px 8px rgba(255,0,0,0.12);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 767px) {
            .application-card { max-width: 100%; }
            .application-card .card-body { padding: 1rem 0.5rem; }
            .pill-row { flex-direction: column; align-items: flex-start; gap: 0.2rem; padding: 0.7rem; }
            .pill-row .field-label { min-width: 0; max-width: 100%; flex: 1 1 0%; margin-bottom: 0.2rem; }
            .pill-row .field-value { margin-left: 0; }
        }
        .filter-form .form-label { font-weight: 600; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">Submitted Applications</h2>
        <form method="get" class="row g-3 align-items-end filter-form mb-4">
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
                    <option value="all"<?= $status_filter === 'all' ? ' selected' : '' ?>>All</option>
                    <option value="Submitted"<?= $status_filter === 'Submitted' ? ' selected' : '' ?>>Submitted</option>
                    <option value="Under Review"<?= $status_filter === 'Under Review' ? ' selected' : '' ?>>Under Review</option>
                    <option value="Shortlisted"<?= $status_filter === 'Shortlisted' ? ' selected' : '' ?>>Shortlisted</option>
                    <option value="Rejected"<?= $status_filter === 'Rejected' ? ' selected' : '' ?>>Rejected</option>
                    <option value="Accepted"<?= $status_filter === 'Accepted' ? ' selected' : '' ?>>Accepted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>
        <div class="row g-3 justify-content-center">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card application-card shadow-sm w-100">
                        <div class="card-header bg-dark text-white">
                            <i class="fas fa-file-alt"></i> Application #<?= $row['id'] ?>
                        </div>
                        <div class="card-body">
                            <div class="application-fields">
                                <div class="pill-row"><span class="field-label">Program</span><span class="field-value"><?= htmlspecialchars($row['program']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Full Name</span><span class="field-value"><?= htmlspecialchars($row['full_name']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Category</span><span class="field-value"><?= htmlspecialchars($row['category']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Campus</span><span class="field-value"><?= htmlspecialchars($row['campus']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Student No.</span><span class="field-value"><?= htmlspecialchars($row['student_number']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Course</span><span class="field-value"><?= htmlspecialchars($row['course']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Year of Study</span><span class="field-value"><?= htmlspecialchars($row['year_of_study']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Graduation Year</span><span class="field-value"><?= htmlspecialchars($row['graduation_year']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Current Job</span><span class="field-value"><?= htmlspecialchars($row['current_job']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Employer</span><span class="field-value"><?= htmlspecialchars($row['employer']) ?></span></div>
                                <div class="pill-row"><span class="field-label">Submitted</span><span class="field-value"><?= htmlspecialchars($row['submission_date'] ?? $row['submitted_at'] ?? '') ?></span></div>
                                <div class="pill-row"><span class="field-label">Status</span><span class="field-value"><span class="badge bg-info text-dark"><?= htmlspecialchars($row['status'] ?? 'N/A') ?></span></span></div>
                                <div class="pill-row"><span class="field-label">Feedback</span><span class="field-value"><?php $fb = $row['feedback'] ?? ''; echo $fb ? htmlspecialchars(mb_strimwidth($fb,0,40,'...')) : '<span class=\'text-muted\'>None</span>'; ?></span></div>
                                <div class="pill-row"><span class="field-label">Attachments</span><span class="field-value"><?php
                                    $conn2 = new mysqli($host, $user, $password, $dbname);
                                    $att_stmt = $conn2->prepare("SELECT id, file_path FROM attachments WHERE application_id = ?");
                                    $att_stmt->bind_param("i", $row['id']);
                                    $att_stmt->execute();
                                    $att_res = $att_stmt->get_result();
                                    $att_count = $att_res->num_rows;
                                    if ($att_count > 0) {
                                        $first = $att_res->fetch_assoc();
                                        echo "<a href='../uploads/" . urlencode($first['file_path']) . "' target='_blank' class='btn btn-sm btn-success'>Download</a> ";
                                        if ($att_count > 1) echo "+" . ($att_count-1) . " more";
                                    } else {
                                        echo "<span class='text-muted'>None</span>";
                                    }
                                    $att_stmt->close();
                                    $conn2->close();
                                ?></span></div>
                                <div class="pill-row"><span class="field-label">Mentor</span><span class="field-value">
<?php
$mentor_name = '-';
if (!empty($row['assigned_mentor'])) {
    $mentor_id = intval($row['assigned_mentor']);
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
                                </span></div>
                            </div>
                            <div class="application-actions text-end">
                                <a href="application_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> View</a>
                                <a href="application_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                <a href="application_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this application?');"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No applications found.</div>
        <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html>
<?php $conn->close(); ?> 