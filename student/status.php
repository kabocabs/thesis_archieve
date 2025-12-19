<?php
require '../functions.php'; 
role('student');

$user_id = $_SESSION['user']['id'];

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['thesis_id'])) {
    $thesis_id = (int)$_POST['thesis_id'];

    // Delete file from uploads
    $file_stmt = $pdo->prepare("SELECT file_path FROM files WHERE thesis_id=?");
    $file_stmt->execute([$thesis_id]);
    $file = $file_stmt->fetch(PDO::FETCH_ASSOC);
    if ($file && file_exists($file['file_path'])) {
        unlink($file['file_path']);
    }

    // Delete from files table
    $pdo->prepare("DELETE FROM files WHERE thesis_id=?")->execute([$thesis_id]);

    // Delete thesis record
    $pdo->prepare("DELETE FROM thesis WHERE id=? AND user_id=?")->execute([$thesis_id, $user_id]);

    log_action("Deleted thesis ID $thesis_id by user $user_id");
    $_SESSION['message'] = "Thesis deleted successfully.";
    header("Location: submission_status.php");
    exit;
}

// Fetch all submissions with latest comments
$stmt = $pdo->prepare("
    SELECT t.*, f.file_path, a.comments AS faculty_comments
    FROM thesis t
    LEFT JOIN files f ON f.thesis_id = t.id
    LEFT JOIN (
        SELECT thesis_id, GROUP_CONCAT(CONCAT(decision, ': ', comments) SEPARATOR ' | ') AS comments
        FROM approvals
        GROUP BY thesis_id
    ) a ON a.thesis_id = t.id
    WHERE t.user_id = ?
    ORDER BY t.id DESC
");
$stmt->execute([$user_id]);
$theses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submission Status</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.container { max-width: 1000px; margin: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 8px; border: 1px solid #ddd; text-align: left; vertical-align: top; }
th { background-color: #f2f2f2; }
.actions form { display: inline-block; margin: 0; }
.actions button { background-color: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
.actions a { margin-right: 5px; text-decoration: none; color: #007bff; }
.message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
.message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.back { display: inline-block; margin-top: 20px; padding: 8px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; }
</style>
</head>
<body>
<div class="container">
<h2>Submission Status</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>

<table>
<tr>
    <th>Title</th>
    <th>Status</th>
    <th>PDF</th>
    <th>Faculty Comments</th>
    <th>Actions</th>
</tr>
<?php if ($theses): ?>
    <?php foreach($theses as $t): ?>
    <tr>
        <td><?= htmlspecialchars($t['title']) ?></td>
        <td><?= htmlspecialchars($t['status'] ?? 'Pending') ?></td>
        <td>
            <?php if (!empty($t['file_path']) && file_exists($t['file_path'])): ?>
                <a href="<?= htmlspecialchars($t['file_path']) ?>" target="_blank">View PDF</a>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($t['faculty_comments'] ?? 'No comments yet') ?></td>
        <td class="actions">
            <a href="edit_thesis.php?id=<?= $t['id'] ?>">Edit</a>
            <form method="post" onsubmit="return confirm('Are you sure you want to delete this thesis?');">
                <input type="hidden" name="thesis_id" value="<?= $t['id'] ?>">
                <button type="submit" name="delete">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr><td colspan="5">No submissions yet.</td></tr>
<?php endif; ?>
</table>

<a href="dashboard.php" class="back">← Back to Dashboard</a>
</div>
</body>
</html>
