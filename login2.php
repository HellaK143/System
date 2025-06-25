<?php
session_start();

$con = new mysqli("localhost", "root", "", "umic");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $con->real_escape_string($_POST['email']);
    $password = $con->real_escape_string($_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='admin'";
    $result = $con->query($query);

    if ($result && $result->num_rows > 0) {
        $_SESSION['admin'] = $email;

        header('Location: /system2/dist/index.html');
        exit();
    } else {
            $_SESSION['error'] = "Incorrect email or password.";
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
