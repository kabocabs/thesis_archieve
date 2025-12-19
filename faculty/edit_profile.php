<?php
require '../functions.php';
auth(); // ensure user is logged in

$user = $_SESSION['user'];
$uid = $user['id'];

$profile_dir = '../uploads/profiles/';
$signature_dir = '../uploads/signatures/';

// Handle form submission
if ($_POST) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name === '' || $email === '') {
        $error = "Name and Email cannot be empty.";
    } else {
        // Handle profile picture upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            if (!is_dir($profile_dir)) mkdir($profile_dir, 0777, true);
            $profile_path = $profile_dir . $uid . '_profile_' . time() . '_' . basename($_FILES['profile_pic']['name']);
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_path);
            $pdo->prepare("UPDATE users SET profile_pic=? WHERE id=?")->execute([$profile_path, $uid]);
        }

        // Handle signature upload
        if (isset($_FILES['signature']) && $_FILES['signature']['error'] === 0) {
            if (!is_dir($signature_dir)) mkdir($signature_dir, 0777, true);
            $signature_path = $signature_dir . $uid . '_signature_' . time() . '_' . basename($_FILES['signature']['name']);
            move_uploaded_file($_FILES['signature']['tmp_name'], $signature_path);
            $pdo->prepare("UPDATE users SET signature=? WHERE id=?")->execute([$signature_path, $uid]);
        }

        // Update name and email
        $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?")->execute([$name, $email, $uid]);

        // Refresh session
        $_SESSION['user'] = $pdo->query("SELECT * FROM users WHERE id=$uid")->fetch();

        // Redirect directly to dashboard
        header('Location: dashboard.php');
        exit;
    }
}

// Determine current profile & signature
$profile_img = !empty($user['profile_pic']) && file_exists($user['profile_pic'])
    ? $user['profile_pic']
    : '../assets/images/default_profile.png';

$signature_img = !empty($user['signature']) && file_exists($user['signature'])
    ? $user['signature']
    : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .profile-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid #0d6efd; }
    .signature-img { max-width: 300px; height: auto; border: 1px solid #ccc; padding: 5px; }
    .card { margin-top: 20px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Edit Profile</span>
        <div class="ms-auto">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-4 text-center">Update Your Profile</h4>
            <form method="post" enctype="multipart/form-data">

                <!-- Profile Picture -->
                <div class="mb-3 text-center">
                    <img src="<?= $profile_img ?>" alt="Profile Picture" class="profile-img mb-2">
                    <input type="file" name="profile_pic" class="form-control mt-2" accept="image/*">
                </div>

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <!-- Signature -->
                <div class="mb-3 text-center">
                    <?php if ($signature_img): ?>
                        <p>Current Signature:</p>
                        <img src="<?= $signature_img ?>" alt="Signature" class="signature-img mb-2">
                    <?php endif; ?>
                    <input type="file" name="signature" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
