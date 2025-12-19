<?php
require '../functions.php';
role('student');

$user = $_SESSION['user'];
$uid  = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $stud  = trim($_POST['student_no']);

    if ($name && $email) {

        // Update text info
        $pdo->prepare("
            UPDATE users 
            SET name=?, email=?, student_no=? 
            WHERE id=?
        ")->execute([$name, $email, $stud, $uid]);

        /* PROFILE IMAGE */
        if (!empty($_FILES['profile_pic']['name'])) {
            if (!is_dir('../uploads/profiles')) mkdir('../uploads/profiles',0777,true);
            $profilePath = "uploads/profiles/{$uid}_profile.png";
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], "../$profilePath");

            $pdo->prepare("UPDATE users SET profile_pic=? WHERE id=?")
                ->execute([$profilePath, $uid]);
        }

        /* SIGNATURE IMAGE */
        if (!empty($_FILES['signature']['name'])) {
            if (!is_dir('../uploads/signatures')) mkdir('../uploads/signatures',0777,true);
            $sigPath = "uploads/signatures/{$uid}_signature.png";
            move_uploaded_file($_FILES['signature']['tmp_name'], "../$sigPath");

            $pdo->prepare("UPDATE users SET signature=? WHERE id=?")
                ->execute([$sigPath, $uid]);
        }

        // Refresh session
        $_SESSION['user'] = $pdo->query("SELECT * FROM users WHERE id=$uid")->fetch();

        header("Location: dashboard_student.php?updated=1");
        exit;
    }

    $err = "Name and Email are required";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="text-center mb-3">Edit Profile</h4>

                    <?php if (!empty($err)): ?>
                        <div class="alert alert-danger"><?= $err ?></div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">

                        <label class="form-label">Full Name</label>
                        <input class="form-control mb-3" name="name"
                               value="<?= htmlspecialchars($user['name']) ?>" required>

                        <label class="form-label">Email</label>
                        <input class="form-control mb-3" type="email" name="email"
                               value="<?= htmlspecialchars($user['email']) ?>" required>

                        <label class="form-label">Student Number</label>
                        <input class="form-control mb-3" name="student_no"
                               value="<?= htmlspecialchars($user['student_no']) ?>">

                        <label class="form-label">Profile Picture</label>
                        <input class="form-control mb-3" type="file" name="profile_pic">

                        <label class="form-label">Signature</label>
                        <input class="form-control mb-3" type="file" name="signature">

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100">
                                Update Profile
                            </button>
                            <a href="dashboard.php"
                               class="btn btn-outline-secondary w-100">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
