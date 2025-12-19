<?php
require '../functions.php'; 
role('admin');

// Fetch logs with user info
$logs = $pdo->query("
    SELECT l.*, u.name 
    FROM activity_logs l 
    JOIN users u ON u.id = l.user_id 
    ORDER BY l.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Activity Logs</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $l): ?>
                            <tr>
                                <td><?= htmlspecialchars($l['name']) ?></td>
                                <td><?= htmlspecialchars($l['action']) ?></td>
                                <td><?= htmlspecialchars($l['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$logs->rowCount()): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> <!-- /.table-responsive -->

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
