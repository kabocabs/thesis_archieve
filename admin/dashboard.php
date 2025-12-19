<?php
require '../functions.php';
role('admin');

$admin = $_SESSION['user'];

$profilePic = (!empty($admin['profile_pic']) && file_exists('../'.$admin['profile_pic']))
    ? '../'.$admin['profile_pic']
    : '../assets/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .menu-btn {
            height: 70px;
            font-size: 1.1rem;
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">🛠 Admin Dashboard</span>

        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <?= htmlspecialchars($admin['name']) ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- MAIN -->
<div class="container py-4">
    <div class="row g-4">

        <!-- PROFILE (ONLY THIS SHOWS INFO) -->
        <div class="col-lg-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">

                    <img src="<?= $profilePic ?>"
                         class="rounded-circle profile-img mb-3 border">

                    <h5 class="fw-bold mb-1">
                        <?= htmlspecialchars($admin['name']) ?>
                    </h5>

                    <p class="text-muted mb-1">
                        <?= htmlspecialchars($admin['email']) ?>
                    </p>

                    <span class="badge bg-dark text-uppercase">
                        <?= htmlspecialchars($admin['role']) ?>
                    </span>

                    <a href="../edit_profile.php"
                       class="btn btn-outline-primary btn-sm w-100 mt-3">
                        <i class="bi bi-person-gear"></i> Edit Profile
                    </a>

                </div>
            </div>
        </div>

        <!-- ADMIN BUTTONS (NO EXTRA INFO) -->
        <div class="col-lg-8">
            <div class="row g-3">

                <div class="col-md-6">
                    <a href="users.php"
                       class="btn btn-primary w-100 menu-btn">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="departments.php"
                       class="btn btn-success w-100 menu-btn">
                        <i class="bi bi-diagram-3"></i> Departments
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="programs.php"
                       class="btn btn-warning w-100 menu-btn">
                        <i class="bi bi-mortarboard"></i> Programs
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="logs.php"
                       class="btn btn-danger w-100 menu-btn">
                        <i class="bi bi-clock-history"></i> System Logs
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>
