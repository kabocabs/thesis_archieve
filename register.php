<?php
require 'config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $role  = $_POST['role'];
    $stud  = $_POST['student_no'] ?? null;

    if (!in_array($role, ['student','faculty','admin'])) {
        echo json_encode(['status'=>'error','message'=>'Invalid role']);
        exit;
    }

    if (!$name || !$email || !$pass) {
        echo json_encode(['status'=>'error','message'=>'All fields required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status'=>'error','message'=>'Email already exists']);
        exit;
    }

    $allowed = ['image/jpeg','image/png'];
    if (!in_array($_FILES['profile_pic']['type'],$allowed)
        || !in_array($_FILES['signature']['type'],$allowed)) {
        echo json_encode(['status'=>'error','message'=>'Images must be JPG or PNG']);
        exit;
    }

    if (!is_dir('uploads/profiles')) mkdir('uploads/profiles',0777,true);
    if (!is_dir('uploads/signatures')) mkdir('uploads/signatures',0777,true);

    $profile_path = 'uploads/profiles/'.time().'_'.$_FILES['profile_pic']['name'];
    $sig_path = 'uploads/signatures/'.time().'_'.$_FILES['signature']['name'];

    move_uploaded_file($_FILES['profile_pic']['tmp_name'],$profile_path);
    move_uploaded_file($_FILES['signature']['tmp_name'],$sig_path);

    $hashed = password_hash($pass,PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (role, student_no, name, email, password, profile_pic, signature)
        VALUES (?,?,?,?,?,?,?)
    ");
    $stmt->execute([
        $role,
        ($role==='student') ? $stud : null,
        $name,
        $email,
        $hashed,
        $profile_path,
        $sig_path
    ]);

    echo json_encode(['status'=>'success']);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="text-center mb-3">Register</h4>

                    <div id="msg"></div>

                    <form id="regForm" enctype="multipart/form-data">
                        <input class="form-control mb-3" name="name" placeholder="Full Name" required>
                        <input class="form-control mb-3" name="email" type="email" placeholder="Email" required>
                        <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>

                        <select class="form-select mb-3" name="role" id="role" required>
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>

                        <input class="form-control mb-3 d-none" name="student_no"
                               id="student_no" placeholder="Student Number">

                        <label>Profile Picture</label>
                        <input class="form-control mb-3" type="file" name="profile_pic" required>

                        <label>Signature</label>
                        <input class="form-control mb-3" type="file" name="signature" required>

                        <button class="btn btn-success w-100">Register</button>
                    </form>

                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', e => {
    document.getElementById('student_no')
        .classList.toggle('d-none', e.target.value !== 'student');
});

document.getElementById('regForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = new FormData(e.target);

    const res = await fetch('register.php', {
        method: 'POST',
        body: form
    });
    const data = await res.json();

    if (data.status === 'success') {
        window.location.href = 'login.php?registered=1';
    } else {
        document.getElementById('msg').innerHTML =
            `<div class="alert alert-danger">${data.message}</div>`;
    }
});
</script>

</body>
</html>
