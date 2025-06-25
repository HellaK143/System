<?php
session_start();

$client_id = "2276603845-kp1k1qeg7vb28futbk86q6umgv33q1ut.apps.googleusercontent.com";

if (!isset($_POST['credential'])) {
    echo '<div class="alert alert-danger m-5">No Google credential received.</div>';
    exit();
}

$token = $_POST['credential'];
$payload = json_decode(base64_decode(explode('.', $token)[1]), true);

if ($payload && $payload['aud'] === $client_id) {
    $email = $payload['email'];
    $con = new mysqli("localhost", "root", "", "umic");
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $con->query($query);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        // Redirect based on role (absolute URLs)
        if ($user['role'] === 'admin') {
            header('Location: http://localhost/system2/index.php');
        } elseif ($user['role'] === 'evaluator') {
            header('Location: http://localhost/system2/dist/evaluator_dashboard.php');
        } elseif ($user['role'] === 'mentor') {
            header('Location: http://localhost/system2/dist/mentor_dashboard.php');
        } else {
            header('Location: http://localhost/system2/dist/entrepreneur_dashboard.php');
        }
        exit();
    } else {
        echo '<div class="alert alert-danger m-5">No account found for this Google email. Please contact admin.<br><a href="../login.php">Back to Login</a></div>';
        exit();
    }
} else {
    echo '<div class="alert alert-danger m-5">Google sign-in failed.<br><a href="../login.php">Back to Login</a></div>';
    exit();
}
?>
