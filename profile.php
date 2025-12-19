<?php
require 'functions.php';
auth();

if ($_POST) {
    $uid = $_SESSION['user']['id'];

    if (!empty($_FILES['profile']['name'])) {
        if (!is_dir('uploads/profiles')) mkdir('uploads/profiles',0777,true);
        $p = 'uploads/profiles/'.$uid.'_profile.png';
        move_uploaded_file($_FILES['profile']['tmp_name'], $p);
        $pdo->prepare("UPDATE users SET profile_pic=? WHERE id=?")
            ->execute([$p,$uid]);
    }

    if (!empty($_FILES['signature']['name'])) {
        if (!is_dir('uploads/signatures')) mkdir('uploads/signatures',0777,true);
        $s = 'uploads/signatures/'.$uid.'_signature.png';
        move_uploaded_file($_FILES['signature']['tmp_name'], $s);
        $pdo->prepare("UPDATE users SET signature=? WHERE id=?")
            ->execute([$s,$uid]);
    }

    $_SESSION['user'] = $pdo->query("SELECT * FROM users WHERE id=$uid")->fetch();
    log_action('Updated profile & signature');

    $success = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-body">

                    <h4 class="text-center mb-3">Complete Your Profile</h4>
                    <p class="text-center text-muted">
                        Upload your profile picture and signature
                    </p>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            Profile updated successfully
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">

                        <!-- Profile Picture -->
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" name="profile" accept="image/*" required>
                            <?php if (!empty($_SESSION['user']['profile_pic'])): ?>
                                <img src="<?= $_SESSION['user']['profile_pic'] ?>"
                                     class="mt-2 rounded border"
                                     style="width:120px;height:120px;object-fit:cover;">
                            <?php endif; ?>
                        </div>

                        <!-- Signature -->
                        <div class="mb-3">
                            <label class="form-label">Signature</label>
                            <input type="file" class="form-control" name="signature" accept="image/*" required>
                            <?php if (!empty($_SESSION['user']['signature'])): ?>
                                <img src="<?= $_SESSION['user']['signature'] ?>"
                                     class="mt-2 border"
                                     style="width:200px;height:80px;object-fit:contain;">
                            <?php endif; ?>
                        </div>

                        <button class="btn btn-primary w-100">
                            Save Changes
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
