<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fields = [
    "program","full_name","category","campus","student_number","course","year_of_study",
    "staff_number","years_at_umu","graduation_year","current_job","employer","faculty",
    "national_id","occupation","marital_status","num_beneficiaries","refugee","disability",
    "disability_text","program_attended","nationality","age_range","phone","email","country",
    "district","subcounty","village","street","business_idea_name","year_of_inception","sector",
    "interested_in","initial_capital","cohort"
];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger m-5">Invalid application ID.</div>');
}
$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update_fields = [];
    $update_values = [];
    foreach ($fields as $field) {
        $update_fields[] = "$field = ?";
        $update_values[] = $_POST[$field] ?? null;
    }
    $update_values[] = $id;
    $sql = "UPDATE applications SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($fields)) . 'i';
    $stmt->bind_param($types, ...$update_values);
    if ($stmt->execute()) {
        header("Location: application_view.php?id=$id&updated=1");
        exit();
    } else {
        $error = "Error updating application: " . $conn->error;
    }
    $stmt->close();
}

$sql = "SELECT id, " . implode(", ", $fields) . " FROM applications WHERE id = ?";
$stmt = $conn->prepare($sql);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../styles.css" rel="stylesheet">
    <style>
        .application-card { max-width: 700px; margin: 2rem auto; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .application-card .card-header {
            background: linear-gradient(90deg, #1a1a1a 60%, #ff0000 100%);
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
            padding: 1.25rem 1.5rem;
        }
        .form-label {
            font-weight: 600; color: #333; margin-bottom: 0; min-width: 0; max-width: 180px;
            background: none;
            border-radius: 0;
            padding: 0.5rem 1rem 0.5rem 1.5rem;
            display: flex;
            align-items: center;
            height: 100%;
            flex: 0 0 160px;
            word-break: break-word;
            white-space: normal;
            overflow-wrap: break-word;
        }
        .pill-field {
            background: #f8f9fa;
            border-radius: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 0.5rem 0.5rem 0.5rem 0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0;
            border: 1px solid #eee;
            max-width: 100%;
            min-height: 48px;
        }
        .pill-field input,
        .pill-field select {
            flex: 1 1 0%;
            border: none;
            background: #f8f9fa;
            font-size: 1.05rem;
            color: #222;
            box-shadow: none;
            outline: none;
            border-radius: 0 1rem 1rem 0;
            padding: 0.5rem 1rem;
            transition: border 0.2s, box-shadow 0.2s;
            max-width: 320px;
            min-width: 120px;
            margin-left: auto;
        }
        .pill-field input:focus,
        .pill-field select:focus {
            background: #fff;
            border: 1px solid #ff0000;
            box-shadow: 0 0 0 0.15rem rgba(255,0,0,0.08);
        }
        .btn-primary {
            background: linear-gradient(90deg, #ff0000 60%, #ff4d4d 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #d90000 60%, #ff6666 100%);
        }
        @media (max-width: 767px) {
            .application-card { max-width: 100%; }
            .pill-field { flex-direction: column; align-items: stretch; gap: 0.5rem; padding: 1rem; }
            .form-label { border-radius: 0; min-width: 0; max-width: 100%; flex: 1 1 0%; padding-bottom: 0.25rem; }
            .pill-field input, .pill-field select { border-radius: 0 0 1rem 1rem; max-width: 100%; min-width: 0; margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card application-card shadow">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-edit"></i> Edit Application #<?= $row['id'] ?>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"> <?= $error ?> </div>
            <?php endif; ?>
            <form method="post" novalidate>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <?php foreach (array_slice($fields, 0, ceil(count($fields)/2)) as $field): ?>
                            <div class="pill-field">
                                <label class="form-label text-capitalize" for="<?= $field ?>"><?= str_replace('_', ' ', $field) ?></label>
                                <input type="text" id="<?= $field ?>" name="<?= $field ?>" value="<?= htmlspecialchars($row[$field]) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <?php foreach (array_slice($fields, ceil(count($fields)/2)) as $field): ?>
                            <div class="pill-field">
                                <label class="form-label text-capitalize" for="<?= $field ?>"><?= str_replace('_', ' ', $field) ?></label>
                                <input type="text" id="<?= $field ?>" name="<?= $field ?>" value="<?= htmlspecialchars($row[$field]) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <a href="application_view.php?id=<?= $row['id'] ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</body>
</html> 