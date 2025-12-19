<?php  
require '../functions.php';
role('admin');

if ($_POST) {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $pass    = $_POST['password'];
    $role    = $_POST['role'];

    // Handle profile picture and signature upload
    $profile_path = $signature_path = null;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        if (!is_dir('../uploads/profiles')) mkdir('../uploads/profiles', 0777, true);
        $profile_path = '../uploads/profiles/' . time() . '_' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_path);
    }

    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        if (!is_dir('../uploads/signatures')) mkdir('../uploads/signatures', 0777, true);
        $signature_path = '../uploads/signatures/' . time() . '_' . basename($_FILES['signature']['name']);
        move_uploaded_file($_FILES['signature']['tmp_name'], $signature_path);
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    $pdo->prepare("
        INSERT INTO users (role, name, email, password, profile_pic, signature)
        VALUES (?, ?, ?, ?, ?, ?)
    ")->execute([$role, $name, $email, $hashed, $profile_path, $signature_path]);

    log_action("Admin added user: $name ($role)");
    $success = "User added successfully!";
}

$users = $pdo->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Add User Form -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Add User</h4>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="student">Student</option>
                                <option value="faculty">Faculty</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_pic" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Signature</label>
                            <input type="file" name="signature" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add User
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Users Table -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Existing Users</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Profile</th>
                                    <th>Signature</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $u): ?>
                                    <tr>
                                        <td>
                                            <?php if ($u['profile_pic'] && file_exists($u['profile_pic'])): ?>
                                                <img src="<?= $u['profile_pic'] ?>" width="50" height="50" class="rounded-circle">
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($u['signature'] && file_exists($u['signature'])): ?>
                                                <img src="<?= $u['signature'] ?>" width="100" height="50">
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($u['name']) ?></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td><?= htmlspecialchars($u['role']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(!$users->rowCount()): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> <!-- /.table-responsive -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
