<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    <title><?= $page_title ?? 'Admin Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="/system2/dist/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <?php if (isset($additional_css)): ?>
        <style><?= $additional_css ?></style>
    <?php endif; ?>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="admin_dashboard.php">Admin</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    $username = $_SESSION['username'] ?? 'Admin';
                    $profile_picture = $_SESSION['profile_picture'] ?? null;
                    $img = $profile_picture ? 'uploads/' . htmlspecialchars($profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=1a1a1a&color=fff&rounded=true&size=32';
                    ?>
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($username) ?>" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                    <span class="d-none d-md-inline fw-semibold"><?= htmlspecialchars($username) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="admin_dashboard.php"><i class="fas fa-tachometer-alt text-primary"></i> <span>Dashboard</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="settings_admin.php"><i class="fas fa-user-cog text-primary"></i> <span>Settings</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="activity_log_admin.php"><i class="fas fa-list-alt text-success"></i> <span>Activity Log</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="notifications_admin.php"><i class="fas fa-bell text-warning"></i> <span>Notifications</span></a></li>
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
                        <div class="sb-sidenav-menu-heading">Admin</div>
                        <a class="nav-link" href="admin_dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Management</div>
                        <!-- Events Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="false" aria-controls="collapseEvents">
                            <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                            Events
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseEvents" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="events_admin.php">View Events</a>
                                <a class="nav-link" href="manage_events_admin.php">Manage Events</a>
                            </nav>
                        </div>
                        <!-- Applications Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseApplications" aria-expanded="false" aria-controls="collapseApplications">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            Applications
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseApplications" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="applications_admin.php">View Applications</a>
                                <a class="nav-link" href="manage_applications_admin.php">Manage Applications</a>
                            </nav>
                        </div>
                        <!-- Sessions Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSessions" aria-expanded="false" aria-controls="collapseSessions">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                            Sessions
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseSessions" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="sessions_admin.php">View Sessions</a>
                                <a class="nav-link" href="manage_sessions_admin.php">Manage Sessions</a>
                            </nav>
                        </div>
                        <!-- Bookings Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBookings" aria-expanded="false" aria-controls="collapseBookings">
                            <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                            Bookings
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseBookings" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="bookings_admin.php">View Bookings</a>
                                <a class="nav-link" href="manage_bookings_admin.php">Manage Bookings</a>
                            </nav>
                        </div>
                        <!-- Resources Link -->
                        <a class="nav-link" href="resources_admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-cube"></i></div>
                            Resources
                        </a>
                        <!-- Mentors Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMentors" aria-expanded="false" aria-controls="collapseMentors">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                            Mentors
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseMentors" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="mentors_admin.php">View Mentors</a>
                                <a class="nav-link" href="manage_mentors_admin.php">Manage Mentors</a>
                            </nav>
                        </div>
                        <!-- Entrepreneurs Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEntrepreneurs" aria-expanded="false" aria-controls="collapseEntrepreneurs">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-graduate"></i></div>
                            Entrepreneurs
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseEntrepreneurs" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="entrepreneurs_admin.php">View Entrepreneurs</a>
                                <a class="nav-link" href="manage_entrepreneurs_admin.php">Manage Entrepreneurs</a>
                            </nav>
                        </div>
                        <!-- Evaluators Dropdown -->
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvaluators" aria-expanded="false" aria-controls="collapseEvaluators">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-check"></i></div>
                            Evaluators
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseEvaluators" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="evaluators_admin.php">View Evaluators</a>
                                <a class="nav-link" href="manage_evaluators_admin.php">Manage Evaluators</a>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading">Communication</div>
                        <a class="nav-link" href="admin_messages.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Messages
                        </a>
                        <a class="nav-link" href="notifications_admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                            Notifications
                        </a>
                        <div class="sb-sidenav-menu-heading">System</div>
                        <a class="nav-link" href="settings_admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                            Settings
                        </a>
                        <a class="nav-link" href="charts_admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                            Charts
                        </a>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Admin
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Admin Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
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
    <script src="/system2/dist/js/scripts.js"></script>
    <?php if (isset($additional_js)): ?>
        <script><?= $additional_js ?></script>
    <?php endif; ?>
</body>
</html> 