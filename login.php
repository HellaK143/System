<?php
session_start();

$errorMessage = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link href="dist/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .show-hide {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
        .google-btn {
            background: #fff;
            border: 1px solid #ddd;
            color: #444;
            font-weight: 500;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            width: 100%;
            justify-content: center;
            transition: box-shadow 0.2s;
        }
        .google-btn:hover {
            box-shadow: 0 2px 8px rgba(66,133,244,0.15);
        }
        .google-icon {
            width: 22px;
            height: 22px;
        }
    </style>
</head>
<body class="bg-primary">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-lg p-4" style="min-width:340px;max-width:400px;width:100%;">
            <div class="card-body">
                <h3 class="text-center mb-4">Login</h3>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"> <?= htmlspecialchars($errorMessage) ?> </div>
                <?php endif; ?>
                <form method="POST" action="dist/login2.php" autocomplete="off">
                    <div class="mb-3">
                        <label for="inputEmail" class="form-label">Email address</label>
                        <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required autofocus />
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="inputPassword" class="form-label">Password</label>
                        <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                        <span class="show-hide" onclick="togglePassword()"><i id="eyeIcon" class="fa fa-eye"></i></span>
                    </div>
                    <div class="d-grid mb-2">
                        <button class="btn btn-primary" type="submit">Login</button>
                    </div>
                </form>
                <button class="google-btn" onclick="window.location.href='dist/google_login.php'">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" class="google-icon" alt="Google logo">
                    Login with Google
                </button>
                <div class="text-center mt-3">
                    <a href="register.html">Need an account? Register</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword() {
            const pwd = document.getElementById('inputPassword');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
