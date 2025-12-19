<?php
require '../functions.php';
role('admin');

// Check ID
if (!isset($_GET['id'])) {
    header('Location: departments.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch department
$stmt = $pdo->prepare("SELECT * FROM departments WHERE id=?");
$stmt->execute([$id]);
$dept = $stmt->fetch();

if (!$dept) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Department not found.</div></div>";
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $pdo->prepare("UPDATE departments SET name=? WHERE id=?")->execute([$name, $id]);
        log_action("Updated department ID: $id to $name");
        header('Location: departments.php');
        exit;
    } else {
        $err = "Department name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <a href="departments.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Departments
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="card-title mb-3 text-center">Edit Department</h4>

                    <?php if (!empty($err)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($dept['name']) ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Update
                            </button>
                            <a href="departments.php" class="btn btn-outline-secondary w-100">
                                Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
