<?php
require '../functions.php';
role('admin');

// Handle Add Department
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $pdo->prepare("INSERT INTO departments(name) VALUES (?)")->execute([$name]);
        log_action("Added department: $name");
    }
    header('Location: departments.php');
    exit;
}

// Handle Delete Department
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM departments WHERE id=?")->execute([$id]);
    log_action("Deleted department ID: $id");
    header('Location: departments.php');
    exit;
}

// Fetch all departments
$deps = $pdo->query("SELECT * FROM departments");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <!-- Back -->
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row g-4">

        <!-- ADD DEPARTMENT -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Add Department</h5>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                        </div>
                        <button type="submit" name="add" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- EXISTING DEPARTMENTS -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Existing Departments</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($deps as $d): ?>
                                    <tr>
                                        <td><?= $d['id'] ?></td>
                                        <td><?= htmlspecialchars($d['name']) ?></td>
                                        <td>
                                            <a href="edit_department.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="?delete=<?= $d['id'] ?>" 
                                               onclick="return confirm('Are you sure?')"
                                               class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(!$deps->rowCount()): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No departments found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> <!-- /.table-responsive -->
                </div>
            </div>
        </div>

    </div> <!-- /.row -->
</div> <!-- /.container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
