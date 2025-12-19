<?php
require '../functions.php';
role('student');
require_profile_complete();

if ($_POST) {

    // Validate uploaded file
    if (!validate_pdf($_FILES['file'])) {
        die('Only PDF allowed (max 10MB)');
    }

    // Insert thesis info into database
    $stmt = $pdo->prepare("
        INSERT INTO thesis 
        (user_id, title, abstract, keywords, adviser, year)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user']['id'],
        $_POST['title'],
        $_POST['abstract'],
        $_POST['keywords'],
        $_POST['adviser'],
        $_POST['year']
    ]);

    $id = $pdo->lastInsertId();

    // Ensure uploads/theses directory exists
    $uploadDir = '../uploads/theses/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $path = $uploadDir . $id . '.pdf';

    // Move uploaded file
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
        die('Failed to move uploaded file. Check folder permissions.');
    }

    // Insert file info into files table
    $pdo->prepare("
        INSERT INTO files(thesis_id, file_path, file_type)
        VALUES (?, ?, ?)
    ")->execute([$id, $path, 'pdf']);

    log_action('Uploaded thesis');

    // Redirect to status page after successful upload
    header("Location: upload_thesis.php?status=success");
    exit;
}

// Fetch thesis submissions for this student
$stmt = $pdo->prepare("SELECT * FROM thesis WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$_SESSION['user']['id']]);
$theses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Thesis</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.container { max-width: 800px; margin: auto; }
form { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
form input, form textarea, form button { display: block; width: 100%; margin-bottom: 10px; padding: 8px; }
form button { background-color: #4CAF50; color: white; border: none; cursor: pointer; width: auto; padding: 8px 15px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
th { background-color: #f2f2f2; }
a.back { display: inline-block; margin-top: 15px; text-decoration: none; padding: 8px 15px; background-color: #007bff; color: white; border-radius: 4px; }
.message { padding: 10px; background-color: #d4edda; color: #155724; margin-bottom: 15px; border-radius: 5px; }
</style>
</head>
<body>
<div class="container">
<h2>Upload Thesis</h2>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="message">Thesis uploaded successfully!</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input name="title" required placeholder="Title">
    <textarea name="abstract" required placeholder="Abstract"></textarea>
    <input name="keywords" required placeholder="Keywords">
    <input name="adviser" required placeholder="Adviser">
    <input name="year" required placeholder="Year">
    <input type="file" name="file" required>
    <button type="submit">Submit</button>
</form>

<h2>Submission Status</h2>
<table>
<tr>
    <th>Title</th>
    <th>Status</th>
</tr>
<?php if ($theses): ?>
    <?php foreach($theses as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['title']) ?></td>
            <td><?= htmlspecialchars($t['status'] ?? 'Pending') ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="2">No submissions yet.</td></tr>
<?php endif; ?>
</table>

<a href="dashboard.php" class="back">← Back </a>
</div>
</body>
</html>
