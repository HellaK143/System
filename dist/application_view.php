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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger m-5">Invalid application ID.</div>');
}
$id = intval($_GET['id']);

$fields = [
    "id", "program", "full_name", "category", "campus", "student_number", "course", "year_of_study",
    "graduation_year", "current_job", "employer", "staff_number", "faculty", "years_at_umu",
    "national_id", "occupation", "marital_status", "num_beneficiaries", "submitted_at", "email"
];

$sql = "SELECT " . implode(", ", $fields) . " FROM applications WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('<div class="alert alert-danger m-5">SQL Prepare Error: ' . htmlspecialchars($conn->error) . '</div>');
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo '<div class="alert alert-warning m-5">Application not found.</div>';
    exit();
}
$row = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!empty($_SESSION['msg_success'])) {
    echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['msg_success']).'</div>';
    unset($_SESSION['msg_success']);
}
if (!empty($_SESSION['msg_error'])) {
    echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['msg_error']).'</div>';
    unset($_SESSION['msg_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
        }
        .application-card {
            max-width: 700px;
            margin: 2.5rem auto;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .application-card .card-header {
            background: linear-gradient(90deg, #1a1a1a 60%, #ff0000 100%);
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
            padding: 1.25rem 1.5rem;
        }
        .application-card .card-body {
            padding: 2rem 2.5rem 1.5rem 2.5rem;
            background: #fff;
        }
        .application-fields {
            font-size: 1.05rem;
        }
        .application-fields .row {
            margin-bottom: 0.7rem;
        }
        .application-fields .col-5 {
            font-weight: 500;
            color: #555;
            text-align: right;
        }
        .application-fields .col-7 {
            color: #222;
            text-align: left;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #ff0000;
            margin: 1.5rem 0 0.7rem 0;
            letter-spacing: 0.5px;
        }
        .badge-info {
            background: #e9ecef;
            color: #1a1a1a;
            font-size: 0.95rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }
        .application-actions a.btn {
            min-width: 120px;
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
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card application-card shadow">
        <div class="card-header text-white d-flex align-items-center justify-content-between">
            <span><i class="fas fa-file-alt me-2"></i> Application #<?= $row['id'] ?></span>
            <span class="badge badge-info bg-light text-dark">Submitted: <?= isset($row['submitted_at']) ? date('d M Y, H:i', strtotime($row['submitted_at'])) : '' ?></span>
        </div>
        <div class="card-body">
            <div class="section-title">Applicant Information</div>
            <div class="row g-2 application-fields">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Full Name</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['full_name']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Category</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['category']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Campus</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['campus']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Student No.</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['student_number']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Course</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['course']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Year of Study</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['year_of_study']) ?></span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Graduation Year</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['graduation_year']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Current Job</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['current_job']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Employer</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['employer']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Staff Number</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['staff_number']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Faculty</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['faculty']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Years at UMU</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['years_at_umu']) ?></span>
                    </div>
                </div>
            </div>
            <div class="section-title">Other Details</div>
            <div class="row g-2 application-fields">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">National ID</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['national_id']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Occupation</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['occupation']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Marital Status</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['marital_status']) ?></span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Beneficiaries</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= htmlspecialchars($row['num_beneficiaries']) ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 bg-light rounded-3 shadow-sm px-3 py-2">
                        <span class="fw-bold text-secondary flex-shrink-0" style="min-width: 120px;">Submitted</span>
                        <span class="ms-3 text-dark flex-grow-1"><?= isset($row['submitted_at']) ? date('d M Y, H:i', strtotime($row['submitted_at'])) : '' ?></span>
                    </div>
                </div>
            </div>
            <!-- Evaluation Scores Summary -->
            <div class="section-title">Evaluation Scores</div>
            <?php
            $conn = new mysqli($host, $user, $password, $dbname);
            if ($conn->connect_error) {
                echo '<div class="alert alert-danger">Could not load evaluation scores.</div>';
            } else {
                // Fetch all criteria
                $criteria = [];
                $res = $conn->query("SELECT id, name FROM evaluation_criteria ORDER BY id ASC");
                while ($rowc = $res->fetch_assoc()) $criteria[$rowc['id']] = $rowc['name'];
                // Fetch all scores for this application
                $scores = [];
                $res = $conn->query("SELECT criteria_id, score FROM evaluation_scores WHERE application_id = " . intval($row['id']));
                while ($sc = $res->fetch_assoc()) {
                    $cid = $sc['criteria_id'];
                    if (!isset($scores[$cid])) $scores[$cid] = [];
                    $scores[$cid][] = $sc['score'];
                }
                if (count($criteria) === 0) {
                    echo '<div class="alert alert-info">No evaluation criteria set.</div>';
                } else {
                    $overall_sum = 0; $overall_count = 0;
                    echo '<div class="table-responsive"><table class="table table-bordered align-middle mb-3"><thead class="table-light"><tr><th>Criteria</th><th>Average Score</th><th>Total Score</th><th>Number of Evaluations</th></tr></thead><tbody>';
                    foreach ($criteria as $cid => $cname) {
                        $vals = isset($scores[$cid]) ? $scores[$cid] : [];
                        $avg = count($vals) ? round(array_sum($vals)/count($vals),2) : '-';
                        $sum = count($vals) ? array_sum($vals) : '-';
                        $num = count($vals);
                        if ($avg !== '-') { $overall_sum += $sum; $overall_count += $num; }
                        echo '<tr><td>' . htmlspecialchars($cname) . '</td><td>' . $avg . '</td><td>' . $sum . '</td><td>' . $num . '</td></tr>';
                    }
                    echo '</tbody></table></div>';
                    if ($overall_count > 0) {
                        $overall_avg = round($overall_sum / $overall_count, 2);
                        echo '<div class="alert alert-success"><strong>Overall Average Score:</strong> ' . $overall_avg . ' <span class="text-muted">(across all criteria and evaluators)</span></div>';
                        echo '<div class="alert alert-secondary"><strong>Overall Total Score:</strong> ' . $overall_sum . '</div>';
                    } else {
                        echo '<div class="alert alert-info">No scores submitted yet.</div>';
                    }
                }
                $conn->close();
            }
            ?>
            <!-- End Evaluation Scores Summary -->
            <div class="section-title">Messages</div>
            <?php
            $applicant_email = $row['email'] ?? '';
            $app_id = $row['id'];
            ?>
            <button class="btn btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#sendMessageModal"><i class="fas fa-envelope"></i> Send Message to Applicant</button>
            <!-- Modal -->
            <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <form method="post" action="send_message.php">
                  <input type="hidden" name="application_id" value="<?= $app_id ?>">
                  <input type="hidden" name="recipient_email" value="<?= htmlspecialchars($applicant_email) ?>">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="sendMessageModalLabel">Send Message to Applicant</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <?php
            // Show previous messages for this application
            $conn2 = new mysqli($host, $user, $password, $dbname);
            if (!$conn2->connect_error) {
                $msg_res = $conn2->query("SELECT message, sent_at FROM messages WHERE application_id = $app_id ORDER BY sent_at DESC");
                if ($msg_res && $msg_res->num_rows > 0) {
                    echo '<div class="mt-3"><h6>Previous Messages</h6><ul class="list-group">';
                    while ($msg = $msg_res->fetch_assoc()) {
                        echo '<li class="list-group-item small"><span class="text-muted">['.htmlspecialchars($msg['sent_at']).']</span> '.htmlspecialchars($msg['message']).'</li>';
                    }
                    echo '</ul></div>';
                }
                $conn2->close();
            }
            ?>
            <div class="section-title">Attachments</div>
            <?php
            $conn = new mysqli($host, $user, $password, $dbname);
            if ($conn->connect_error) {
                echo '<div class="alert alert-danger">Could not load attachments.</div>';
            } else {
                $stmt = $conn->prepare("SELECT id, file_type, file_path, uploaded_at FROM attachments WHERE application_id = ? ORDER BY uploaded_at DESC");
                if (!$stmt) {
                    echo '<div class="alert alert-danger">SQL Error (attachments): ' . htmlspecialchars($conn->error) . '</div>';
                } else {
                    $stmt->bind_param("i", $row['id']);
                    $stmt->execute();
                    $attachments = $stmt->get_result();
                    if ($attachments->num_rows > 0) {
                        echo '<ul class="list-group mb-3">';
                        while ($att = $attachments->fetch_assoc()) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo '<span><i class="fas fa-paperclip"></i> ' . htmlspecialchars($att['file_type']) . ' - ' . htmlspecialchars($att['file_path']) . ' <small class="text-muted ms-2">(' . $att['uploaded_at'] . ')</small></span>';
                            echo '<a href="../uploads/' . urlencode($att['file_path']) . '" class="btn btn-sm btn-success" download>Download</a>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="alert alert-info">No attachments uploaded yet.</div>';
                    }
                    $stmt->close();
                }
                $conn->close();
            }
            ?>
            <div class="section-title">Status & Feedback</div>
            <?php
            // Fetch current status and feedback
            $conn = new mysqli($host, $user, $password, $dbname);
            $status = '';
            $feedback = '';
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("SELECT status, feedback FROM applications WHERE id = ?");
                if (!$stmt) {
                    echo '<div class="alert alert-danger">SQL Error (status/feedback): ' . htmlspecialchars($conn->error) . '</div>';
                } else {
                    $stmt->bind_param("i", $row['id']);
                    $stmt->execute();
                    $stmt->bind_result($status, $feedback);
                    $stmt->fetch();
                    $stmt->close();
                }
                $conn->close();
            }
            ?>
            <form method="post" action="application_update_status.php" class="mb-4">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="mb-3">
                    <label for="status" class="form-label">Application Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php
                        $statuses = ['Submitted','Under Review','Shortlisted','Rejected','Accepted'];
                        foreach ($statuses as $s) {
                            $sel = ($status === $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $sel>$s</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="feedback" class="form-label">Feedback</label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="3" placeholder="Enter feedback for the applicant (optional)"><?= htmlspecialchars($feedback) ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Update Status & Feedback</button>
            </form>
            <?php if ($feedback): ?>
                <div class="alert alert-info"><strong>Current Feedback:</strong> <?= nl2br(htmlspecialchars($feedback)) ?></div>
            <?php endif; ?>
            <a href="attachments_upload.php?application_id=<?= $row['id'] ?>" class="btn btn-primary mb-3"><i class="fas fa-upload"></i> Upload Attachment</a>
            <div class="section-title">Assignment</div>
            <?php
            // Fetch current evaluator and mentor assignments (usernames)
            $conn = new mysqli($host, $user, $password, $dbname);
            $assigned_evaluator = $assigned_mentor = null;
            $evaluator_name = $mentor_name = '';
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("SELECT assigned_evaluator, assigned_mentor FROM applications WHERE id = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $stmt->bind_result($assigned_evaluator, $assigned_mentor);
                $stmt->fetch();
                $stmt->close();
                // Fetch evaluator username
                if ($assigned_evaluator) {
                    $s = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
                    $s->bind_param("i", $assigned_evaluator);
                    $s->execute();
                    $s->bind_result($evaluator_name);
                    $s->fetch();
                    $s->close();
                }
                // Fetch mentor name
                $mentor_name = '-';
                if ($assigned_mentor) {
                    $mentor_id = intval($assigned_mentor);
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
                // Fetch all evaluators and mentors for dropdowns
                $evaluators = $mentors = [];
                $res = $conn->query("SELECT user_id, username FROM users WHERE role = 'evaluator' ORDER BY username");
                while ($r = $res->fetch_assoc()) $evaluators[] = $r;
                $res = $conn->query("SELECT user_id, username FROM users WHERE role = 'mentor' ORDER BY username");
                while ($r = $res->fetch_assoc()) $mentors[] = $r;
                $conn->close();
            }
            ?>
            <form method="post" action="application_assign.php" class="mb-4">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="mb-3">
                    <label for="assigned_evaluator" class="form-label">Evaluator</label>
                    <select class="form-select" id="assigned_evaluator" name="assigned_evaluator">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($evaluators as $ev): ?>
                            <option value="<?= $ev['user_id'] ?>" <?= ($assigned_evaluator == $ev['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($ev['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($evaluator_name): ?>
                        <div class="form-text">Current: <strong><?= htmlspecialchars($evaluator_name) ?></strong></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="assigned_mentor" class="form-label">Mentor</label>
                    <select class="form-select" id="assigned_mentor" name="assigned_mentor">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($mentors as $m): ?>
                            <option value="<?= $m['user_id'] ?>" <?= ($assigned_mentor == $m['user_id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($mentor_name): ?>
                        <div class="form-text">Current: <strong><?= htmlspecialchars($mentor_name) ?></strong></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Assign</button>
            </form>
            <div class="application-actions text-end mt-4">
                <a href="applications.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
                <a href="application_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                <a href="application_delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this application?');"><i class="fas fa-trash"></i> Delete</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html> 