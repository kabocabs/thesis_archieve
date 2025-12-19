<?php 
require '../functions.php'; 
role('admin');

// Handle Add, Edit, Delete operations
if ($_POST) { 
    if (isset($_POST['action'])) { 
        $action = $_POST['action'];

        if ($action == 'delete') {
            if (!empty($_POST['program_id'])) {
                $prog_id = (int)$_POST['program_id'];
                $stmt = $pdo->prepare("DELETE FROM programs WHERE id=?");
                $stmt->execute([$prog_id]);
                log_action('Deleted program ID: ' . $prog_id);
                $_SESSION['message'] = 'Program deleted successfully.';
            } else {
                $_SESSION['error'] = 'Program ID is required for deletion.';
            }
        } else {
            // Add/Edit
            $dept_name = trim($_POST['dept']);
            $prog_name = trim($_POST['name']);
            if ($dept_name === '' || $prog_name === '') {
                $_SESSION['error'] = 'Department and program name cannot be empty.';
                header("Location: programs.php");
                exit;
            }

            $stmt = $pdo->prepare("SELECT id FROM departments WHERE name=?");
            $stmt->execute([$dept_name]);
            $dept = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dept) {
                $pdo->prepare("INSERT INTO departments(name) VALUES (?)")->execute([$dept_name]);
                log_action("Added new department: $dept_name");
                $dept_id = $pdo->lastInsertId();
            } else {
                $dept_id = $dept['id'];
            }

            if ($action == 'add') {
                $stmt = $pdo->prepare("INSERT INTO programs(department_id, name) VALUES (?, ?)");
                $stmt->execute([$dept_id, $prog_name]);
                log_action('Added program: ' . $prog_name);
                $_SESSION['message'] = 'Program added successfully.';
            } elseif ($action == 'edit') {
                $prog_id = (int)$_POST['program_id'];
                $stmt = $pdo->prepare("UPDATE programs SET department_id=?, name=? WHERE id=?");
                $stmt->execute([$dept_id, $prog_name, $prog_id]);
                log_action('Edited program ID: ' . $prog_id . ' to ' . $prog_name);
                $_SESSION['message'] = 'Program updated successfully.';
            }
        }

        header("Location: programs.php");
        exit;
    }
}

// Fetch programs
$progs = $pdo->query("SELECT p.*, d.name AS dname FROM programs p JOIN departments d ON d.id=p.department_id ORDER BY d.name, p.name")->fetchAll(PDO::FETCH_ASSOC);

// Handle edit request
$edit_program = null; 
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') { 
    $stmt = $pdo->prepare("SELECT p.*, d.name AS dname FROM programs p JOIN departments d ON d.id=p.department_id WHERE p.id=?"); 
    $stmt->execute([$_GET['id']]); 
    $edit_program = $stmt->fetch(PDO::FETCH_ASSOC); 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Programs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card { margin-bottom: 20px; }
    .table td, .table th { vertical-align: middle; }
</style>
</head>
<body class="bg-light">

<div class="container py-4">

    <!-- Back Button -->
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Add/Edit Program -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title"><?= $edit_program ? 'Edit Program' : 'Add New Program' ?></h4>
            <form method="post" class="row g-3 mt-2">
                <input type="hidden" name="action" value="<?= $edit_program ? 'edit' : 'add' ?>">
                <?php if ($edit_program): ?>
                    <input type="hidden" name="program_id" value="<?= $edit_program['id'] ?>">
                <?php endif; ?>

                <div class="col-md-6">
                    <label class="form-label" for="dept">Department:</label>
                    <input type="text" name="dept" id="dept" class="form-control" value="<?= $edit_program ? htmlspecialchars($edit_program['dname']) : '' ?>" placeholder="Type department name" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="name">Program Name:</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= $edit_program ? htmlspecialchars($edit_program['name']) : '' ?>" placeholder="Program Name" required>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $edit_program ? 'Update Program' : 'Add Program' ?>
                    </button>
                    <?php if ($edit_program): ?>
                        <a href="programs.php" class="btn btn-secondary ms-2">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Programs Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Existing Programs</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Department</th>
                            <th>Program Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($progs)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No programs found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($progs as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['dname']) ?></td>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td class="d-flex gap-2">
                                        <a href="programs.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="program_id" value="<?= $p['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>