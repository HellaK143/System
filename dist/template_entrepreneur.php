<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entrepreneur') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= $page_title ?? 'Entrepreneur Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/admin-custom.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <?php if (isset($additional_css)): ?>
        <style><?= $additional_css ?></style>
    <?php endif; ?>
</head>
<body class="sb-nav-fixed entrepreneur-dashboard">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="entrepreneur_dashboard2.php">Entrepreneur</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=Entrepreneur&background=1a1a1a&color=fff&rounded=true&size=32" alt="Entrepreneur" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                    <span class="d-none d-md-inline fw-semibold">Entrepreneur</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="entrepreneur_dashboard2.php"><i class="fas fa-tachometer-alt text-primary"></i> <span>Dashboard</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="my_applications_entrepreneur.php"><i class="fas fa-folder-open text-info"></i> <span>My Applications</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="submit_application_entrepreneur.php"><i class="fas fa-plus-circle text-success"></i> <span>Submit Application</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="messages_entrepreneur.php"><i class="fas fa-envelope text-success"></i> <span>Messages</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="notifications_entrepreneur.php"><i class="fas fa-bell text-warning"></i> <span>Notifications</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="activity_log_entrepreneur.php"><i class="fas fa-list-alt text-secondary"></i> <span>Activity Log</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="settings_entrepreneur.php"><i class="fas fa-user-cog text-primary"></i> <span>Settings</span></a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="logout.php"><i class="fas fa-sign-out-alt text-danger"></i> <span>Logout</span></a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Entrepreneur</div>
                        <a class="nav-link" href="entrepreneur_dashboard2.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link" href="my_applications_entrepreneur.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-folder-open"></i></div>
                            My Applications
                        </a>
                        <a class="nav-link" href="submit_application_entrepreneur.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus-circle"></i></div>
                            Submit Application
                        </a>
                        <a class="nav-link" href="messages_entrepreneur.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Messages
                        </a>
                        <a class="nav-link" href="notifications_entrepreneur.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                            Notifications
                        </a>
                        <a class="nav-link" href="activity_log_entrepreneur.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                            Activity Log
                        </a>
                        <a class="nav-link" href="settings_entrepreneur.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                            Settings
                        </a>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Entrepreneur
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Entrepreneur Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="entrepreneur_dashboard2.php">Dashboard</a></li>
                        <?php if (isset($breadcrumb_items)): ?>
                            <?php foreach ($breadcrumb_items as $item): ?>
                                <li class="breadcrumb-item"> <?= $item ?> </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                    <?php if (isset($page_content)): ?>
                        <?= $page_content ?>
                    <?php endif; ?>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; UMU Innovation Office 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <?php if (isset($additional_js)): ?>
        <script><?= $additional_js ?></script>
    <?php endif; ?>
</body>
</html>