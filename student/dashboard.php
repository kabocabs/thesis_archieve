<?php 
require '../functions.php'; 
role('student'); 
$user = $_SESSION['user'];

// Safe profile image
$profilePic = !empty($user['profile_pic']) 
    ? "../".$user['profile_pic'] 
    : "../assets/default-avatar.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .dashboard-card:hover {
            transform: translateY(-3px);
            transition: .2s ease-in-out;
        }
    </style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">
            🎓 Student Dashboard
        </span>

        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3">
                <?= htmlspecialchars($user['name']) ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container py-4">
    <div class="row g-4">

        <!-- PROFILE INFO -->
        <div class="col-lg-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">

                    <img src="<?= $profilePic ?>" class="rounded-circle profile-img mb-3 border">

                    <h5 class="fw-bold mb-0">
                        <?= htmlspecialchars($user['name']) ?>
                    </h5>

                    <small class="text-muted">
                        <?= htmlspecialchars($user['email']) ?>
                    </small>

                    <div class="mt-2">
                        <span class="badge bg-success text-uppercase">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </div>

                    <?php if (!empty($user['student_no'])): ?>
                        <div class="mt-3">
                            <small class="text-muted">Student Number</small>
                            <div class="fw-semibold">
                                <?= htmlspecialchars($user['student_no']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="edit_profile.php"
                       class="btn btn-outline-primary btn-sm mt-3 w-100">
                        <i class="bi bi-person-gear"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- ACTION CARDS -->
        <div class="col-lg-8">
            <div class="row g-4">

                <!-- UPLOAD THESIS -->
                <div class="col-md-6">
                    <div class="card shadow-sm dashboard-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>

                            <h5 class="mt-3 fw-bold">
                                Upload Thesis
                            </h5>

                            <p class="text-muted small">
                                Submit your thesis document for evaluation.
                            </p>

                            <a href="upload_thesis.php"
                               class="btn btn-primary w-100">
                                Upload Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- THESIS STATUS -->
                <div class="col-md-6">
                    <div class="card shadow-sm dashboard-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clipboard-check fs-1 text-success"></i>

                            <h5 class="mt-3 fw-bold">
                                Thesis Status
                            </h5>

                            <p class="text-muted small">
                                Track approval and review progress.
                            </p>

                            <a href="status.php"
                               class="btn btn-success w-100">
                                View Status
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>
