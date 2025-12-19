<?php 
require '../functions.php'; 
role('faculty'); 

$faculty_info = $_SESSION['user']; 
$profile_pic_base_path = '../uploads/profiles/'; 
$signature_base_path = '../uploads/signatures/'; 

// Determine profile picture path
$profile_img = '../assets/images/default_profile.png'; // default
if (!empty($faculty_info['profile_pic']) && file_exists($profile_pic_base_path . basename($faculty_info['profile_pic']))) {
    $profile_img = $profile_pic_base_path . basename($faculty_info['profile_pic']);
}

// Determine signature path
$signature_img = '';
if (!empty($faculty_info['signature']) && file_exists($signature_base_path . basename($faculty_info['signature']))) {
    $signature_img = $signature_base_path . basename($faculty_info['signature']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .profile-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid #0d6efd; }
    .signature-img { max-width: 300px; height: auto; border: 1px solid #ccc; padding: 5px; }
    .card { margin-bottom: 20px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">👨‍🏫 Faculty Dashboard</span>
        <div class="ms-auto">
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- Profile Card -->
    <div class="card shadow-sm text-center">
        <div class="card-body">
            <img src="<?= $profile_img ?>" alt="Profile Picture" class="profile-img mb-3">
            <h4 class="fw-bold"><?= htmlspecialchars($faculty_info['name']) ?></h4>
            <p class="text-muted mb-1"><?= htmlspecialchars($faculty_info['email']) ?></p>
            <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($faculty_info['role']) ?></span>
        </div>
    </div>

    <!-- Dashboard Actions -->
    <div class="row mt-4 g-4">

        <!-- Review Theses -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-journal-text fs-1 text-success"></i>
                    <h5 class="mt-3 fw-bold">Review Theses</h5>
                    <p class="text-muted">Access and review assigned thesis submissions.</p>
                    <a href="review.php" class="btn btn-success w-100">Go to Review</a>
                </div>
            </div>
        </div>

        <!-- Profile Edit -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-person-gear fs-1 text-primary"></i>
                    <h5 class="mt-3 fw-bold">Edit Profile</h5>
                    <p class="text-muted">Update your profile or signature.</p>
                    <a href="edit_profile.php" class="btn btn-primary w-100">Edit Profile</a>
                </div>
            </div>
        </div>

    </div>

    

</div>

</body>
</html>
