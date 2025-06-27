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
    <title>Login - UMU Innovation Office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="styles.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            padding: 3rem 2.5rem;
            max-width: 450px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1.5rem;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(255, 0, 0, 0.3);
        }

        .login-title {
            color: #1a1a1a;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #1a1a1a, #ff0000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            color: #666;
            font-size: 1rem;
            font-weight: 400;
        }

        .form-label {
            color: #1a1a1a;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.25);
            background: #ffffff;
        }

        .form-control::placeholder {
            color: #999;
        }

        .password-field {
            position: relative;
        }

        .show-hide {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 0.5rem;
        }

        .show-hide:hover {
            color: #ff0000;
        }

        .btn-login {
            background: linear-gradient(135deg, #1a1a1a, #ff0000);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.4);
            color: white;
        }

        .google-btn {
            background: #ffffff;
            border: 2px solid #e0e0e0;
            color: #333;
            font-weight: 500;
            border-radius: 12px;
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
            width: 100%;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .google-btn:hover {
            border-color: #ff0000;
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.2);
            color: #333;
            text-decoration: none;
        }

        .google-icon {
            width: 24px;
            height: 24px;
        }

        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .register-link a {
            color: #ff0000;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #cc0000;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .login-title {
                font-size: 1.8rem;
            }

            .login-logo {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="logo.png" alt="UMU Logo" class="login-logo">
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to your UMU Innovation Office account</p>
            </div>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="dist/login2.php" autocomplete="off">
                <div class="mb-3">
                    <label for="inputEmail" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input class="form-control" id="inputEmail" name="email" type="email" 
                           placeholder="Enter your email address" required autofocus />
                </div>
                
                <div class="mb-4 password-field">
                    <label for="inputPassword" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input class="form-control" id="inputPassword" name="password" type="password" 
                           placeholder="Enter your password" required />
                    <button type="button" class="show-hide" onclick="togglePassword()">
                        <i id="eyeIcon" class="fas fa-eye"></i>
                    </button>
                </div>
                
                <div class="d-grid mb-3">
                    <button class="btn btn-login" type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>
            </form>

            <div class="divider">
                <span>or continue with</span>
            </div>

            <a href="dist/google_login.php" class="google-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" 
                     class="google-icon" alt="Google logo">
                Sign in with Google
            </a>

            <div class="register-link">
                <p class="mb-0">
                    Don't have an account? 
                    <a href="register.html">Create one here</a>
                </p>
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
