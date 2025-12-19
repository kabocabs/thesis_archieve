<?php
require 'config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $u = $stmt->fetch();

    if ($u && password_verify($_POST['password'], $u['password'])) {
        $_SESSION['user'] = $u;
        echo json_encode([
            'status' => 'success',
            'redirect' => "{$u['role']}/dashboard.php"
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email or password'
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="text-center mb-3">Login</h4>

                    <div id="msg"></div>

                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success">
                            Registration successful. Please login.
                        </div>
                    <?php endif; ?>

                    <form id="loginForm">
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>

                    <p class="text-center mt-3">
                        No account? <a href="register.php">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = new FormData(e.target);

    const res = await fetch('login.php', {
        method: 'POST',
        body: form
    });
    const data = await res.json();

    if (data.status === 'success') {
        window.location.href = data.redirect;
    } else {
        document.getElementById('msg').innerHTML =
            `<div class="alert alert-danger">${data.message}</div>`;
    }
});
</script>

</body>
</html>
