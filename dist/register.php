<?php
session_start();
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="card p-4" style="width: 400px;">
    <h3 class="text-center mb-4">Create an Account</h3>

    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form action="register2.php" method="POST">
      <div class="mb-3">
        <input type="text" class="form-control" name="username" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email address" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <select class="form-select" name="role" required>
          <option value="" disabled selected>Select role</option>
          <option value="mentor">Mentor</option>
          <option value="entrepreneur">Entrepreneur</option>
          <option value="evaluator">Evaluator</option>
        </select>
      </div>
      <button class="btn btn-primary w-100" type="submit">Register</button>
    </form>
    <div class="mt-3 text-center">
      <a href="login.php">Already have an account? Log in</a>
    </div>
  </div>
</div>
</body>
</html>