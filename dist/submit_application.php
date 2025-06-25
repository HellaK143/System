<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "umic";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect all POST data
$fields = [
    "program","full_name","category","campus","student_number","course","year_of_study",
    "staff_number","years_at_umu","graduation_year","current_job","employer","faculty",
    "national_id","occupation","marital_status","num_beneficiaries","refugee","disability",
    "disability_text","program_attended","nationality","age_range","phone","email","country",
    "district","subcounty","village","street","business_idea_name","year_of_inception","sector",
    "interested_in","initial_capital","cohort"
];

// Bind all the fields as strings
$types = str_repeat('s', count($fields));
$values = [];

foreach ($fields as $field) {
    $values[] = $_POST[$field] ?? null;
}

// Build column and placeholder lists
$cols = implode(",", $fields);
$placeholders = rtrim(str_repeat('?,', count($fields)), ",");

$sql = "INSERT INTO applications ($cols) VALUES ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    $app_id = $stmt->insert_id;
    // Handle file uploads
    if (!empty($_FILES['attachments']['name'][0])) {
        $allowed_types = [
            'pdf','doc','docx','ppt','pptx','xls','xlsx','jpg','jpeg','png','gif','mp4','avi','mov','txt','zip','rar','csv'
        ];
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
            $name = $_FILES['attachments']['name'][$i];
            $tmp = $_FILES['attachments']['tmp_name'][$i];
            $error = $_FILES['attachments']['error'][$i];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($error === UPLOAD_ERR_OK && in_array($ext, $allowed_types)) {
                $new_name = uniqid('att_') . '.' . $ext;
                $target = $upload_dir . $new_name;
                if (move_uploaded_file($tmp, $target)) {
                    $file_type = $ext;
                    $conn->query("INSERT INTO attachments (application_id, file_type, file_path) VALUES ($app_id, '$file_type', '$new_name')");
                }
            }
        }
    }
    header("Location: /system2/application.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();