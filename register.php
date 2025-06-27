<?php
$errorMessage = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - UMU Innovation Office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            background: url('uploads/umu-fos-large.jpg') center center/cover no-repeat fixed;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(120deg, rgba(30,30,30,0.7) 0%, rgba(255,0,0,0.18) 100%);
            z-index: 0;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        .register-card {
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(18px) saturate(1.2);
            -webkit-backdrop-filter: blur(18px) saturate(1.2);
            border-radius: 22px;
            box-shadow: 0 12px 48px rgba(0,0,0,0.22), 0 1.5px 8px rgba(255,0,0,0.08);
            border: 1.5px solid rgba(255,255,255,0.25);
            padding: 1.2rem 1rem 1rem 1rem;
            max-width: 340px;
            width: 100%;
            transition: box-shadow 0.3s, transform 0.3s, border 0.3s;
            animation: fadeInUp 0.8s cubic-bezier(.4,0,.2,1);
        }
        .register-card:hover {
            transform: translateY(-6px) scale(1.022);
            box-shadow: 0 24px 64px rgba(255,0,0,0.16), 0 8px 32px rgba(0,0,0,0.18);
            border: 2.5px solid #ff0000;
        }
        .register-header {
            text-align: center;
            margin-bottom: 0.7rem;
        }
        .register-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.1rem;
            background: linear-gradient(135deg, #1a1a1a, #ff0000 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }
        .register-title::after {
            content: '';
            display: block;
            width: 38px;
            height: 4px;
            margin: 0.18rem auto 0 auto;
            border-radius: 2px;
            background: #F8ED17;
        }
        .form-label {
            color: #222;
            font-weight: 600;
            font-size: 0.97rem;
            margin-bottom: 0.2rem;
        }
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 13px;
            padding: 0.5rem 1rem;
            font-size: 0.98rem;
            background: rgba(255,255,255,0.92);
            transition: border 0.22s, box-shadow 0.22s, background 0.22s;
        }
        .form-control:hover {
            border-color: #ff0000;
            background: #fffbe6;
        }
        .form-control:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.16rem rgba(255,0,0,0.13);
            background: #fff;
        }
        .form-control::placeholder {
            color: #aaa;
        }
        .position-relative .show-hide {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
            padding: 0.5rem;
            transition: color 0.22s;
        }
        .position-relative .show-hide:hover {
            color: #ff0000;
        }
        .btn-register {
            background: linear-gradient(135deg, #1a1a1a, #ff0000 90%);
            border: none;
            border-radius: 13px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.98rem;
            color: white;
            transition: box-shadow 0.22s, transform 0.22s, background 0.22s;
            box-shadow: 0 2px 10px rgba(255,0,0,0.13);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn-register::after {
            content: '';
            position: absolute;
            left: 50%; top: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 200%; height: 200%;
            background: rgba(255,0,0,0.10);
            border-radius: 50%;
            transition: transform 0.4s cubic-bezier(.4,0,.2,1);
            z-index: -1;
        }
        .btn-register:hover, .btn-register:active {
            background: #F8ED17 !important;
            color: #1a1a1a !important;
            box-shadow: 0 8px 25px rgba(248,237,23,0.22);
            transform: translateY(-2px) scale(1.03);
        }
        .btn-register:hover::after, .btn-register:active::after {
            transform: translate(-50%, -50%) scale(1);
        }
        .btn-register:focus {
            outline: 2.5px solid #F8ED17;
            outline-offset: 2px;
        }
        .register-link {
            text-align: center;
            margin-top: 0.7rem;
            padding-top: 0.4rem;
            border-top: 1px solid #e0e0e0;
        }
        .register-link a {
            color: #ff0000;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.22s;
        }
        .register-link a:hover {
            color: #cc0000;
            text-decoration: underline;
        }
        .alert {
            border-radius: 13px;
            border: none;
            font-weight: 500;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h3 class="register-title">Create Account</h3>
            </div>
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="dist/register_user.php" autocomplete="off">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input class="form-control" id="firstName" name="first_name" type="text" required />
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input class="form-control" id="lastName" name="last_name" type="text" required />
                </div>
                <div class="mb-3">
                    <label for="inputEmail" class="form-label">Email address</label>
                    <input class="form-control" id="inputEmail" name="email" type="email" required />
                </div>
                <div class="mb-3 position-relative">
                    <label for="inputPassword" class="form-label">Password</label>
                    <input class="form-control" id="inputPassword" name="password" type="password" required minlength="6" />
                    <button type="button" class="show-hide" onclick="togglePassword()"><i id="eyeIcon" class="fa fa-eye"></i></button>
                </div>
                <div class="mb-3">
                    <label for="inputPasswordConfirm" class="form-label">Confirm Password</label>
                    <input class="form-control" id="inputPasswordConfirm" name="confirm_password" type="password" required minlength="6" />
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="entrepreneur">Entrepreneur</option>
                        <option value="mentor">Mentor</option>
                        <option value="evaluator">Evaluator</option>
                    </select>
                </div>
                <div class="d-grid mb-2">
                    <button class="btn btn-register" type="submit">Register</button>
                </div>
            </form>
            <div class="register-link">
                <p class="mb-0">
                    Already have an account?
                    <a href="login.php">Login</a>
                </p>
            </div>
        </div>
    </div>
    <footer class="text-center text-white-50 small mt-4" style="z-index:2; position:relative;">
        <div style="background:rgba(0,0,0,0.18); border-radius:12px; display:inline-block; padding:0.5em 1.2em; margin-bottom:0.7em;">
            &copy; <?= date('Y') ?> UMU Innovation Office &middot; <a href="#" style="color:#ffb3b3; text-decoration:underline;">Privacy Policy</a> &middot; <a href="#" style="color:#ffb3b3; text-decoration:underline;">Terms</a>
        </div>
    </footer>
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