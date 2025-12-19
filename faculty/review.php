<?php
require '../functions.php';
role('faculty');
require_profile_complete();

// Handle approval/rejection
if ($_POST) {
    $thesis_id = (int)$_POST['id'];
    $decision  = $_POST['decision'];
    $comments  = trim($_POST['comments']);

    if ($comments !== '') {
        $pdo->prepare("
            INSERT INTO approvals(thesis_id, faculty_id, decision, comments)
            VALUES (?, ?, ?, ?)
        ")->execute([
            $thesis_id,
            $_SESSION['user']['id'],
            $decision,
            $comments
        ]);

        $pdo->prepare("UPDATE thesis SET status=? WHERE id=?")
            ->execute([$decision, $thesis_id]);

        log_action("Reviewed thesis #$thesis_id with decision: $decision");

        $_SESSION['message'] = "Thesis #$thesis_id has been $decision.";
        header("Location: review.php");
        exit;
    } else {
        $_SESSION['error'] = "Comments cannot be empty.";
    }
}

// Fetch pending theses
$theses = $pdo->query("
    SELECT t.*, u.name 
    FROM thesis t 
    JOIN users u ON u.id=t.user_id 
    WHERE status='pending'
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review Theses</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.container { max-width: 800px; margin: auto; }
form { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
textarea { width: 100%; height: 80px; margin-bottom: 10px; padding: 8px; }
button { padding: 8px 15px; margin-right: 5px; border: none; border-radius: 4px; cursor: pointer; }
button.approve { background-color: #4CAF50; color: white; }
button.reject { background-color: #f44336; color: white; }
.message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
.message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.back { display: inline-block; margin-top: 20px; padding: 8px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; }
</style>
</head>
<body>
<div class="container">
<h2>Review Pending Theses</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if (empty($theses)): ?>
    <p>No pending theses to review.</p>
<?php else: ?>
    <?php foreach ($theses as $t): ?>
        <form method="post">
            <strong><?= htmlspecialchars($t['title']) ?></strong> by <?= htmlspecialchars($t['name']) ?><br>
            <textarea name="comments" required placeholder="Enter your comments"></textarea><br>
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <button type="submit" name="decision" value="approved" class="approve">Approve</button>
            <button type="submit" name="decision" value="rejected" class="reject">Reject</button>
        </form>
    <?php endforeach; ?>
<?php endif; ?>

<a href="dashboard.php" class="back">← Back to Dashboard</a>
</div>
</body>
</html>
